<?php
declare(strict_types=1);
namespace App;
final class Render {
 public static function col(?string $c,string $def):string{ return $c&&preg_match('/^#[0-9a-fA-F]{6}$/',$c)?$c:$def; }
 public static function qr(array $q,array $o):string{
  $n=$q['n'];$g=$q['m'];$mg=max(0,min(10,(int)($o['margin']??4)));$sz=max(100,min(2000,(int)($o['size']??400)));$t=$n+2*$mg;
  $fg=self::col($o['fg']??null,'#000000');$bg=self::col($o['bg']??null,'#ffffff');$ec=self::col($o['eye']??null,$fg);
  $shape=$o['shape']??'square';$es=$o['eyeShape']??'square';
  $s="<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 $t $t\" width=\"$sz\" height=\"$sz\">";
  if(empty($o['transparent']))$s.="<rect width=\"$t\" height=\"$t\" fill=\"$bg\"/>";
  $d='';
  for($y=0;$y<$n;$y++)for($x=0;$x<$n;$x++){
   if(!$g[$y][$x]||($x<7&&$y<7)||($x>=$n-7&&$y<7)||($x<7&&$y>=$n-7))continue;$X=$x+$mg;$Y=$y+$mg;
   $d.=match($shape){'dot'=>'M'.($X+.1).' '.($Y+.5).'a.4 .4 0 1 0 .8 0a.4 .4 0 1 0 -.8 0',
    'rounded'=>'M'.($X+.3).' '.$Y.'h.4a.3 .3 0 0 1 .3 .3v.4a.3 .3 0 0 1 -.3 .3h-.4a.3 .3 0 0 1 -.3 -.3v-.4a.3 .3 0 0 1 .3 -.3z',
    default=>"M$X {$Y}h1v1h-1z"};}
  $s.="<path fill=\"$fg\" d=\"$d\"/>";
  foreach([[0,0],[$n-7,0],[0,$n-7]] as [$ox,$oy]){$X=$ox+$mg;$Y=$oy+$mg;
   if($es==='circle'){$s.="<circle cx=\"".($X+3.5)."\" cy=\"".($Y+3.5)."\" r=\"3\" fill=\"none\" stroke=\"$ec\" stroke-width=\"1\"/><circle cx=\"".($X+3.5)."\" cy=\"".($Y+3.5)."\" r=\"1.5\" fill=\"$ec\"/>";}
   else{$r=$es==='rounded'?1.5:0;$s.="<rect x=\"".($X+.5)."\" y=\"".($Y+.5)."\" width=\"6\" height=\"6\" rx=\"$r\" fill=\"none\" stroke=\"$ec\" stroke-width=\"1\"/><rect x=\"".($X+2)."\" y=\"".($Y+2)."\" width=\"3\" height=\"3\" rx=\"".($r?.8:0)."\" fill=\"$ec\"/>";}}
  return $s.'</svg>'; }
 public static function bar(array $b,array $o):string{
  $bits=$b['bits'];$len=strlen($bits);$mg=max(0,min(20,(int)($o['margin']??10)));$sz=max(200,min(2000,(int)($o['size']??400)));
  $fg=self::col($o['fg']??null,'#000000');$bg=self::col($o['bg']??null,'#ffffff');$h=60;$text=!empty($o['text']);$H=$h+($text?14:0)+$mg*2;$W=$len+$mg*2;
  $s="<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 $W $H\" width=\"$sz\" height=\"".(int)round($sz*$H/$W)."\">";
  if(empty($o['transparent']))$s.="<rect width=\"$W\" height=\"$H\" fill=\"$bg\"/>";
  $d='';for($i=0;$i<$len;){if($bits[$i]==='1'){$j=$i;while($j<$len&&$bits[$j]==='1')$j++;$d.='M'.($i+$mg).' '.$mg.'h'.($j-$i)."v{$h}h-".($j-$i).'z';$i=$j;}else $i++;}
  $s.="<path fill=\"$fg\" d=\"$d\"/>";
  if($text)$s.="<text x=\"".($W/2)."\" y=\"".($h+$mg+11)."\" text-anchor=\"middle\" font-family=\"monospace\" font-size=\"10\" fill=\"$fg\">".htmlspecialchars($b['label'],ENT_QUOTES)."</text>";
  return $s.'</svg>'; }
}
