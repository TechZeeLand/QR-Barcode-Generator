<?php
declare(strict_types=1);
namespace App;
final class Render {
 public static function col(?string $c,string $def):string{ return $c&&preg_match('/^#[0-9a-fA-F]{6}$/',$c)?$c:$def; }
 public static function logo(?string $l):string{ return $l&&strlen($l)<=400000&&preg_match('~^data:image/(png|jpeg|webp|svg\+xml);base64,[A-Za-z0-9+/=]+$~',$l)?$l:''; }
 public static function qr(array $q,array $o):string{
  $n=$q['n'];$g=$q['m'];$mg=max(0,min(10,(int)($o['margin']??4)));$sz=max(100,min(2000,(int)($o['size']??400)));$t=$n+2*$mg;
  $fg=self::col($o['fg']??null,'#000000');$bg=self::col($o['bg']??null,'#ffffff');$ec=self::col($o['eye']??null,$fg);
  $fill=empty($o['grad'])?$fg:'url(#g)';$shape=$o['shape']??'square';$es=$o['eyeShape']??'square';
  $frame=in_array($o['frame']??'',['square','rounded'],true)?$o['frame']:'';$cap=trim(substr((string)($o['caption']??''),0,40));
  $off=$frame?1:0;$W=$t+2*$off;$H=$W+($cap!==''?3.5:0);
  $s="<svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" viewBox=\"0 0 $W $H\" width=\"$sz\" height=\"".(int)round($sz*$H/$W)."\">";
  if(!empty($o['grad']))$s.='<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="'.$fg.'"/><stop offset="1" stop-color="'.self::col($o['fg2']??null,'#0b3d91').'"/></linearGradient></defs>';
  if(empty($o['transparent']))$s.="<rect width=\"$W\" height=\"$H\" fill=\"$bg\"/>";
  if($frame)$s.="<rect x=\".5\" y=\".5\" width=\"".($W-1)."\" height=\"".($H-1)."\" rx=\"".($frame==='rounded'?2:0)."\" fill=\"none\" stroke=\"$fg\" stroke-width=\"1\"/>";
  $s.="<g transform=\"translate($off $off)\">";$d='';
  for($y=0;$y<$n;$y++)for($x=0;$x<$n;$x++){
   if(!$g[$y][$x]||($x<7&&$y<7)||($x>=$n-7&&$y<7)||($x<7&&$y>=$n-7))continue;$X=$x+$mg;$Y=$y+$mg;
   $d.=match($shape){'dot'=>'M'.($X+.1).' '.($Y+.5).'a.4 .4 0 1 0 .8 0a.4 .4 0 1 0 -.8 0',
    'rounded'=>'M'.($X+.3).' '.$Y.'h.4a.3 .3 0 0 1 .3 .3v.4a.3 .3 0 0 1 -.3 .3h-.4a.3 .3 0 0 1 -.3 -.3v-.4a.3 .3 0 0 1 .3 -.3z',
    default=>"M$X {$Y}h1v1h-1z"};}
  $s.="<path fill=\"$fill\" d=\"$d\"/>";
  foreach([[0,0],[$n-7,0],[0,$n-7]] as [$ox,$oy]){$X=$ox+$mg;$Y=$oy+$mg;
   if($es==='circle')$s.="<circle cx=\"".($X+3.5)."\" cy=\"".($Y+3.5)."\" r=\"3\" fill=\"none\" stroke=\"$ec\" stroke-width=\"1\"/><circle cx=\"".($X+3.5)."\" cy=\"".($Y+3.5)."\" r=\"1.5\" fill=\"$ec\"/>";
   else{$r=$es==='rounded'?1.5:0;$s.="<rect x=\"".($X+.5)."\" y=\"".($Y+.5)."\" width=\"6\" height=\"6\" rx=\"$r\" fill=\"none\" stroke=\"$ec\" stroke-width=\"1\"/><rect x=\"".($X+2)."\" y=\"".($Y+2)."\" width=\"3\" height=\"3\" rx=\"".($r?.8:0)."\" fill=\"$ec\"/>";}}
  if($lg=self::logo($o['logo']??null)){$L=round($n*0.22,2);$p=($t-$L)/2;
   $s.="<rect x=\"".($p-.6)."\" y=\"".($p-.6)."\" width=\"".($L+1.2)."\" height=\"".($L+1.2)."\" rx=\"1\" fill=\"$bg\"/><image x=\"$p\" y=\"$p\" width=\"$L\" height=\"$L\" preserveAspectRatio=\"xMidYMid meet\" href=\"$lg\" xlink:href=\"$lg\"/>";}
  $s.='</g>';
  if($cap!=='')$s.="<text x=\"".($W/2)."\" y=\"".($W+1.5)."\" text-anchor=\"middle\" dominant-baseline=\"middle\" font-family=\"Arial,Helvetica,sans-serif\" font-size=\"2\" font-weight=\"700\" fill=\"$fg\">".htmlspecialchars($cap,ENT_QUOTES)."</text>";
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
