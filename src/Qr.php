<?php
declare(strict_types=1);
namespace App;
/** Minimal QR Code encoder (byte mode, versions 1-40, ECC L/M/Q/H). */
final class Qr {
 private const FMT=['L'=>1,'M'=>0,'Q'=>3,'H'=>2];
 private const EPB=['L'=>[0,7,10,15,20,26,18,20,24,30,18,20,24,26,30,22,24,28,30,28,28,28,28,30,30,26,28,30,30,30,30,30,30,30,30,30,30,30,30,30,30],
  'M'=>[0,10,16,26,18,24,16,18,22,22,26,30,22,22,24,24,28,28,26,26,26,26,28,28,28,28,28,28,28,28,28,28,28,28,28,28,28,28,28,28,28],
  'Q'=>[0,13,22,18,26,18,24,18,22,20,24,28,26,24,20,30,24,28,28,26,30,28,30,30,30,30,28,30,30,30,30,30,30,30,30,30,30,30,30,30,30],
  'H'=>[0,17,28,22,16,22,28,26,26,24,28,24,28,22,24,24,30,28,28,26,28,30,24,30,30,30,30,30,30,30,30,30,30,30,30,30,30,30,30,30,30]];
 private const NB=['L'=>[0,1,1,1,1,1,2,2,2,2,4,4,4,4,4,6,6,6,6,7,8,8,9,9,10,12,12,12,13,14,15,16,17,18,19,19,20,21,22,24,25],
  'M'=>[0,1,1,1,2,2,4,4,4,5,5,5,8,9,9,10,10,11,13,14,16,17,17,18,20,21,23,25,26,28,29,31,33,35,37,38,40,43,45,47,49],
  'Q'=>[0,1,1,2,2,4,4,6,6,8,8,8,10,12,16,12,17,16,18,21,20,23,23,25,27,29,34,34,35,38,40,43,45,48,51,53,56,59,62,65,68],
  'H'=>[0,1,1,2,4,4,4,5,6,8,8,11,11,16,16,18,16,19,21,25,25,25,34,30,32,35,37,40,42,45,48,51,54,57,60,63,66,70,74,77,81]];
 private static function raw(int $v):int{ $r=(16*$v+128)*$v+64; if($v>=2){$a=intdiv($v,7)+2;$r-=(25*$a-10)*$a-55; if($v>=7)$r-=36;} return $r; }
 private static function mul(int $x,int $y):int{ $z=0; for($i=7;$i>=0;$i--){$z=($z<<1)^(($z>>7)*0x11D);$z^=(($y>>$i)&1)*$x;} return $z; }
 private static function rs(array $d,int $deg):array{
  $div=array_fill(0,$deg,0);$div[$deg-1]=1;$root=1;
  for($i=0;$i<$deg;$i++){for($j=0;$j<$deg;$j++){$div[$j]=self::mul($div[$j],$root);if($j+1<$deg)$div[$j]^=$div[$j+1];}$root=self::mul($root,2);}
  $r=array_fill(0,$deg,0);
  foreach($d as $b){$f=$b^array_shift($r);$r[]=0;foreach($div as $i=>$c)$r[$i]^=self::mul($c,$f);}
  return $r; }
 private static function fmt(array &$g,int $n,string $ecl,int $mask):void{
  $d=(self::FMT[$ecl]<<3)|$mask;$r=$d;for($i=0;$i<10;$i++)$r=($r<<1)^(($r>>9)*0x537);
  $b=(($d<<10)|$r)^0x5412;$s=function($x,$y,$v)use(&$g){$g[$y][$x]=$v;};$bit=fn($i)=>(($b>>$i)&1)==1;
  for($i=0;$i<=5;$i++)$s(8,$i,$bit($i));$s(8,7,$bit(6));$s(8,8,$bit(7));$s(7,8,$bit(8));
  for($i=9;$i<15;$i++)$s(14-$i,8,$bit($i));
  for($i=0;$i<8;$i++)$s($n-1-$i,8,$bit($i));for($i=8;$i<15;$i++)$s(8,$n-15+$i,$bit($i));$s(8,$n-8,true); }
 private static function pen(array $g,int $n):int{ $p=0;
  for($t=0;$t<2;$t++)for($a=0;$a<$n;$a++){$run=1;for($b=1;$b<$n;$b++){
   $c=$t?$g[$b][$a]:$g[$a][$b];$q=$t?$g[$b-1][$a]:$g[$a][$b-1];
   if($c===$q){$run++;if($run==5)$p+=3;elseif($run>5)$p++;}else $run=1;}}
  $d=0;for($y=0;$y<$n;$y++)for($x=0;$x<$n;$x++){ if($g[$y][$x])$d++;
   if($y<$n-1&&$x<$n-1&&$g[$y][$x]===$g[$y][$x+1]&&$g[$y][$x]===$g[$y+1][$x]&&$g[$y][$x]===$g[$y+1][$x+1])$p+=3;}
  $t=$n*$n;return $p+(int)(ceil(abs($d*20-$t*10)/$t)-1)*10; }
 /** @return array{n:int,m:array} */
 public static function encode(string $text,string $ecl='M'):array{
  if(!isset(self::FMT[$ecl]))$ecl='M';
  $bytes=$text===''?[]:array_values(unpack('C*',$text));
  for($v=1;;$v++){ if($v>40)throw new \RuntimeException('Content is too long for a QR code at this error-correction level.');
   $cap=intdiv(self::raw($v),8)-self::EPB[$ecl][$v]*self::NB[$ecl][$v];$cc=$v<10?8:16;
   if(4+$cc+8*count($bytes)<=$cap*8)break; }
  $bits=[];$put=function(int $val,int $k)use(&$bits){for($i=$k-1;$i>=0;$i--)$bits[]=($val>>$i)&1;};
  $put(4,4);$put(count($bytes),$cc);foreach($bytes as $b)$put($b,8);
  $put(0,min(4,$cap*8-count($bits)));while(count($bits)%8)$bits[]=0;
  for($p=0xEC;count($bits)<$cap*8;$p^=0xEC^0x11)$put($p,8);
  $data=[];foreach(array_chunk($bits,8) as $c)$data[]=bindec(implode('',$c));
  // split into blocks, add ECC, interleave
  $nb=self::NB[$ecl][$v];$el=self::EPB[$ecl][$v];$rawc=intdiv(self::raw($v),8);$ns=$nb-$rawc%$nb;$sl=intdiv($rawc,$nb);
  $blocks=[];$k=0;for($i=0;$i<$nb;$i++){$len=$sl-$el+($i<$ns?0:1);$dat=array_slice($data,$k,$len);$k+=$len;$e=self::rs($dat,$el);if($i<$ns)$dat[]=0;$blocks[]=array_merge($dat,$e);}
  $all=[];for($i=0;$i<count($blocks[0]);$i++)foreach($blocks as $j=>$bl)if($i!=$sl-$el||$j>=$ns)$all[]=$bl[$i];
  // function patterns
  $n=$v*4+17;$m=array_fill(0,$n,array_fill(0,$n,false));$f=$m;
  $set=function(int $x,int $y,bool $d)use(&$m,&$f,$n){if($x>=0&&$x<$n&&$y>=0&&$y<$n){$m[$y][$x]=$d;$f[$y][$x]=true;}};
  for($i=0;$i<$n;$i++){$set(6,$i,$i%2==0);$set($i,6,$i%2==0);}
  foreach([[3,3],[$n-4,3],[3,$n-4]] as [$cx,$cy])for($dy=-4;$dy<=4;$dy++)for($dx=-4;$dx<=4;$dx++){$d=max(abs($dx),abs($dy));$set($cx+$dx,$cy+$dy,$d!=2&&$d!=4);}
  $pos=[];if($v>1){$na=intdiv($v,7)+2;$step=$v==32?26:(int)ceil(($v*4+4)/($na*2-2))*2;$pos=[6];for($q=$n-7;count($pos)<$na;$q-=$step)array_splice($pos,1,0,[$q]);}
  $L=count($pos)-1;foreach($pos as $i=>$px)foreach($pos as $j=>$py){if(($i==0&&$j==0)||($i==0&&$j==$L)||($i==$L&&$j==0))continue;
   for($dy=-2;$dy<=2;$dy++)for($dx=-2;$dx<=2;$dx++)$set($px+$dx,$py+$dy,max(abs($dx),abs($dy))!=1);}
  $tmp=$m;self::fmt($tmp,$n,$ecl,0);for($y=0;$y<$n;$y++)for($x=0;$x<$n;$x++)if($tmp[$y][$x]!==$m[$y][$x]||$y==8||$x==8||$y==$n-8||$x>=$n-8&&$y==8)$f[$y][$x]=true;
  for($y=0;$y<$n;$y++)for($x=0;$x<$n;$x++)if($x==8&&$y<9||$y==8&&($x<9||$x>=$n-8)||$x==8&&$y>=$n-8)$f[$y][$x]=true;
  if($v>=7){$r=$v;for($i=0;$i<12;$i++)$r=($r<<1)^(($r>>11)*0x1F25);$vb=($v<<12)|$r;
   for($i=0;$i<18;$i++){$d=(($vb>>$i)&1)==1;$a=$n-11+$i%3;$b=intdiv($i,3);$set($a,$b,$d);$set($b,$a,$d);}}
  // codewords, zigzag
  $i=0;$tot=count($all)*8;
  for($right=$n-1;$right>=1;$right-=2){if($right==6)$right=5;
   for($vert=0;$vert<$n;$vert++)for($j=0;$j<2;$j++){$x=$right-$j;$y=((($right+1)&2)==0)?$n-1-$vert:$vert;
    if(!$f[$y][$x]){$m[$y][$x]=$i<$tot&&((($all[$i>>3]>>(7-($i&7)))&1)==1);$i++;}}}
  // best mask
  $best=null;$bp=PHP_INT_MAX;
  for($k=0;$k<8;$k++){$g=$m;
   for($y=0;$y<$n;$y++)for($x=0;$x<$n;$x++){if($f[$y][$x])continue;
    $inv=match($k){0=>($x+$y)%2==0,1=>$y%2==0,2=>$x%3==0,3=>($x+$y)%3==0,4=>(intdiv($x,3)+intdiv($y,2))%2==0,5=>$x*$y%2+$x*$y%3==0,6=>($x*$y%2+$x*$y%3)%2==0,default=>(($x+$y)%2+$x*$y%3)%2==0};
    if($inv)$g[$y][$x]=!$g[$y][$x];}
   self::fmt($g,$n,$ecl,$k);$s=self::pen($g,$n);if($s<$bp){$bp=$s;$best=$g;}}
  return ['n'=>$n,'m'=>$best]; }
}
