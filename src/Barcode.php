<?php
declare(strict_types=1);
namespace App;
/** 1D barcode encoders. Each returns a string of '1' (bar) / '0' (space) modules. */
final class Barcode {
 public const TYPES=['code128'=>'Code 128 (auto B/C)','code39'=>'Code 39','ean13'=>'EAN-13','ean8'=>'EAN-8','upca'=>'UPC-A','itf'=>'Interleaved 2 of 5','itf14'=>'ITF-14','codabar'=>'Codabar','msi'=>'MSI Plessey','pharmacode'=>'Pharmacode'];
 private const C128='212222 222122 222221 121223 121322 131222 122213 122312 132212 221213 221312 231212 112232 122132 122231 113222 123122 123221 223211 221132 221231 213212 223112 312131 311222 321122 321221 312212 322112 322211 212123 212321 232121 111323 131123 131321 112313 132113 132311 211313 231113 231311 112133 112331 132131 113123 113321 133121 313121 211331 231131 213113 213311 213131 311123 311321 331121 312113 312311 332111 314111 221411 431111 111224 111422 121124 121421 141122 141221 112214 112412 122114 122411 142112 142211 241211 221114 413111 241112 134111 111242 121142 121241 114212 124112 124211 411212 421112 421211 212141 214121 412121 111143 111341 131141 114113 114311 411113 411311 113141 114131 311141 411131 211412 211214 211232 2331112';
 private const C39=['0'=>'000110100','1'=>'100100001','2'=>'001100001','3'=>'101100000','4'=>'000110001','5'=>'100110000','6'=>'001110000','7'=>'000100101','8'=>'100100100','9'=>'001100100','A'=>'100001001','B'=>'001001001','C'=>'101001000','D'=>'000011001','E'=>'100011000','F'=>'001011000','G'=>'000001101','H'=>'100001100','I'=>'001001100','J'=>'000011100','K'=>'100000011','L'=>'001000011','M'=>'101000010','N'=>'000010011','O'=>'100010010','P'=>'001010010','Q'=>'000000111','R'=>'100000110','S'=>'001000110','T'=>'000010110','U'=>'110000001','V'=>'011000001','W'=>'111000000','X'=>'010010001','Y'=>'110010000','Z'=>'011010000','-'=>'010000101','.'=>'110000100',' '=>'011000100','*'=>'010010100','$'=>'010101000','/'=>'010100010','+'=>'010001010','%'=>'000101010'];
 private const L=['0001101','0011001','0010011','0111101','0100011','0110001','0101111','0111011','0110111','0001011'];
 private const PAR=['LLLLLL','LLGLGG','LLGGLG','LLGGGL','LGLLGG','LGGLLG','LGGGLL','LGLGLG','LGLGGL','LGGLGL'];
 private static function runs(string $widths):string{ $s='';$bar=true;foreach(str_split($widths) as $w){$s.=str_repeat($bar?'1':'0',(int)$w);$bar=!$bar;}return $s; }
 /** @return array{bits:string,label:string} */
 public static function encode(string $type,string $d):array{
  if($d==='')throw new \InvalidArgumentException('Enter some content first.');
  switch($type){
  case 'code128':
   $p=explode(' ',self::C128);$codes=[];
   if(ctype_digit($d)&&strlen($d)%2==0&&strlen($d)>=4){$codes[]=105;foreach(str_split($d,2) as $pr)$codes[]=(int)$pr;}
   else{$codes[]=104;foreach(str_split($d) as $ch){$o=ord($ch)-32;if($o<0||$o>95)throw new \InvalidArgumentException('Code 128 supports printable ASCII only.');$codes[]=$o;}}
   $sum=$codes[0];foreach($codes as $i=>$c)if($i)$sum+=$c*$i;
   $codes[]=$sum%103;$codes[]=106;return ['bits'=>self::runs(implode('',array_map(fn($c)=>$p[$c],$codes))),'label'=>$d];
  case 'code39':
   $d=strtoupper($d);$s='';foreach(str_split('*'.$d.'*') as $k=>$ch){if(!isset(self::C39[$ch])||($ch==='*'&&$k&&$k<strlen($d)+1))throw new \InvalidArgumentException('Code 39 supports 0-9, A-Z, space and - . $ / + % only.');
    $w='';foreach(str_split(self::C39[$ch]) as $x)$w.=$x?'3':'1';$s.=self::runs($w).'0';}
   return ['bits'=>rtrim($s,'0'),'label'=>'*'.$d.'*'];
  case 'upca': case 'ean13':
   $d=preg_replace('/\D/','',$d);$len=$type==='upca'?11:12;
   if(strlen($d)===$len+1)$d=substr($d,0,$len); if(strlen($d)!==$len)throw new \InvalidArgumentException(($type==='upca'?'UPC-A':'EAN-13')." needs $len digits (the check digit is added automatically).");
   $full=$type==='upca'?'0'.$d:$d;$sum=0;foreach(str_split($full) as $i=>$c)$sum+=(int)$c*($i%2?3:1);$full.=(string)((10-$sum%10)%10);
   $par=self::PAR[(int)$full[0]];$s='101';
   for($i=1;$i<=6;$i++){$l=self::L[(int)$full[$i]];$s.=$par[$i-1]==='L'?$l:strrev(strtr($l,'01','10'));}
   $s.='01010';for($i=7;$i<=12;$i++)$s.=strtr(self::L[(int)$full[$i]],'01','10');$s.='101';
   return ['bits'=>$s,'label'=>$type==='upca'?substr($full,1):$full];
  case 'ean8':
   $d=preg_replace('/\D/','',$d);if(strlen($d)===8)$d=substr($d,0,7);if(strlen($d)!==7)throw new \InvalidArgumentException('EAN-8 needs 7 digits (the check digit is added automatically).');
   $sum=0;foreach(str_split($d) as $i=>$c)$sum+=(int)$c*($i%2?1:3);$full=$d.((10-$sum%10)%10);
   $s='101';for($i=0;$i<4;$i++)$s.=self::L[(int)$full[$i]];$s.='01010';for($i=4;$i<8;$i++)$s.=strtr(self::L[(int)$full[$i]],'01','10');
   return ['bits'=>$s.'101','label'=>$full];
  case 'itf': case 'itf14':
   $d=preg_replace('/\D/','',$d);if($d===''||strlen($d)>80)throw new \InvalidArgumentException('Enter digits only (max 80).');
   if($type==='itf14'){if(strlen($d)===14)$d=substr($d,0,13);if(strlen($d)!==13)throw new \InvalidArgumentException('ITF-14 needs 13 digits (the check digit is added automatically).');
    $sum=0;foreach(str_split($d) as $i=>$c)$sum+=(int)$c*($i%2?1:3);$d.=(string)((10-$sum%10)%10);}
   if(strlen($d)%2)$d='0'.$d;
   $pt=['00110','10001','01001','11000','00101','10100','01100','00011','10010','01010'];$s='1010';
   foreach(str_split($d,2) as $pr)for($k=0;$k<5;$k++)$s.=str_repeat('1',$pt[(int)$pr[0]][$k]==='1'?3:1).str_repeat('0',$pt[(int)$pr[1]][$k]==='1'?3:1);
   return ['bits'=>$s.'11101','label'=>$d];
  case 'codabar':
   $d=strtoupper($d);if(!preg_match('/^[A-D]/',$d))$d='A'.$d.'A';
   $cb=['0'=>'0000011','1'=>'0000110','2'=>'0001001','3'=>'1100000','4'=>'0010010','5'=>'1000010','6'=>'0100001','7'=>'0100100','8'=>'0110000','9'=>'1001000','-'=>'0001100','$'=>'0011000',':'=>'1000101','/'=>'1010001','.'=>'1010100','+'=>'0010101','A'=>'0011010','B'=>'0101001','C'=>'0001011','D'=>'0001110'];
   $s='';foreach(str_split($d) as $ch){if(!isset($cb[$ch]))throw new \InvalidArgumentException('Codabar supports 0-9 - $ : / . + and start/stop letters A-D.');$w='';foreach(str_split($cb[$ch]) as $x)$w.=$x==='1'?'3':'1';$s.=self::runs($w).'0';}
   return ['bits'=>rtrim($s,'0'),'label'=>$d];
  case 'msi':
   $d=preg_replace('/\D/','',$d);if($d===''||strlen($d)>40)throw new \InvalidArgumentException('MSI needs digits only (max 40).');
   $sum=0;foreach(str_split(strrev($d)) as $i=>$c){$c=(int)$c;$sum+=$i%2==0?2*$c-($c>=5?9:0):$c;}
   $full=$d.((10-$sum%10)%10);$s='110';foreach(str_split($full) as $c)foreach(str_split(str_pad(decbin((int)$c),4,'0',STR_PAD_LEFT)) as $b)$s.=$b==='1'?'110':'100';
   return ['bits'=>$s.'1001','label'=>$full];
  case 'pharmacode':
   $n=(int)preg_replace('/\D/','',$d);if($n<3||$n>131070)throw new \InvalidArgumentException('Pharmacode needs a number from 3 to 131070.');
   $lab=(string)$n;$bars=[];while($n>0){if($n%2){$bars[]=1;$n=intdiv($n-1,2);}else{$bars[]=3;$n=intdiv($n-2,2);}}
   return ['bits'=>implode('00',array_map(fn($w)=>str_repeat('1',$w),array_reverse($bars))),'label'=>$lab];
  }
  throw new \InvalidArgumentException('Unknown barcode type.'); }
}
