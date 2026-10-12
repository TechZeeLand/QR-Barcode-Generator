<?php
declare(strict_types=1);
namespace App;
final class Layout {
 public const SITE='QR & Barcode Generator';
 public static function e(string $s):string{ return htmlspecialchars($s,ENT_QUOTES); }
 public static function icon(string $n,string $cls=''):string{
  foreach(['ui','social','other'] as $d){$f=__DIR__."/../public/assets/icons/$d/$n.svg";
   if(is_file($f))return (string)preg_replace_callback('/^<svg\b([^>]*)>/',fn($m)=>'<svg class="icon icon-'.$n.($cls?" $cls":'').'" aria-hidden="true" focusable="false" width="1em" height="1em"'.preg_replace('/\s(?:width|height|class)="[^"]*"/','',$m[1]).'>',(string)file_get_contents($f),1);}
  return ''; }
 public static function head(string $title,string $desc='Free, open-source QR code and barcode generator. Customize colours, shapes and logos, then download SVG or PNG. No tracking, nothing stored.'):void{
  $t=self::e($title);$d=self::e($desc);
  echo <<<H
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>$t</title><meta name="description" content="$d">
<meta property="og:type" content="website"><meta property="og:title" content="$t"><meta property="og:description" content="$d"><meta name="twitter:card" content="summary"><meta name="twitter:title" content="$t"><meta name="twitter:description" content="$d">
<link rel="icon" type="image/svg+xml" href="/assets/img/logo.svg"><link rel="manifest" href="/assets/favicon/site.webmanifest">
<meta name="theme-color" content="#0b3d91" media="(prefers-color-scheme: light)"><meta name="theme-color" content="#0f1420" media="(prefers-color-scheme: dark)">
<link rel="stylesheet" href="/assets/css/style.css"><script src="/assets/js/theme-init.js"></script></head><body>
<a class="skip-link" href="#main-content">Skip to content</a>
<header class="topbar"><div class="topbar-inner"><div class="site-title"><a class="brand-link" href="/index.php"><img class="brand-logo" src="/assets/img/logo.svg" alt="" width="26" height="26"><span>
H;
  echo self::SITE.'</span></a></div><div class="topbar-actions"><a class="gh-link" href="https://github.com/TechZeeLand/QR-Barcode-Generator" target="_blank" rel="noopener">'.self::icon('github').'<span>Source on GitHub</span></a>';
  echo '<button type="button" class="theme-toggle" id="theme-toggle" aria-pressed="false" aria-label="Switch to dark mode" title="Switch theme">'.self::icon('sun').self::icon('moon').'</button></div></div></header>'; }
 public static function foot():void{
  $soc=['youtube'=>['YouTube','https://www.youtube.com/@TechZeeLand'],'facebook'=>['Facebook','https://www.facebook.com/TechZeeLand'],'instagram'=>['Instagram','https://www.instagram.com/TechZeeLand'],'tiktok'=>['TikTok','https://www.tiktok.com/@TechZeeLand'],'github'=>['GitHub','https://github.com/TechZeeLand']];
  echo '<footer class="site-footer"><div class="footer-inner"><div class="footer-cols"><nav class="footer-col" aria-label="Site pages"><h3>Pages</h3><ul class="footer-links">';
  foreach(['/index.php'=>'Home','/about.php'=>'About','/contact.php'=>'Contact','/privacy-policy.php'=>'Privacy Policy','/terms-and-conditions.php'=>'Terms &amp; Conditions','/cookie-policy.php'=>'Cookie Policy'] as $u=>$l)echo "<li><a href=\"$u\">$l</a></li>";
  echo '</ul></nav><nav class="footer-col" aria-label="Social links"><h3>Connect</h3><ul class="social-links">';
  foreach($soc as $k=>[$l,$u])echo "<li><a href=\"$u\" target=\"_blank\" rel=\"noopener\">".self::icon($k)." $l</a></li>";
  echo '<li><a href="https://pathaopay.me/@azlanaziz" target="_blank" rel="noopener"><span class="icon-box" aria-hidden="true"><span class="pathaopay-icon"></span></span> Support</a></li></ul></nav></div>';
  echo '<div class="footer-bottom"><p>&copy; '.date('Y').' TechZeeLand. '.self::SITE.' is free &amp; open source under the <a href="https://www.gnu.org/licenses/agpl-3.0.html" target="_blank" rel="noopener">AGPL-3.0</a> license.</p><p><a href="https://github.com/TechZeeLand" target="_blank" rel="noopener">Made by TechZeeLand</a></p></div></div></footer>';
  echo '<script src="/assets/js/theme.js"></script></body></html>'; }
 public static function page(string $title,string $desc,string $h1,string $lede,string $html):void{
  self::head("$title — ".self::SITE,$desc);
  echo '<main id="main-content" class="content-wrap"><h1>'.self::e($h1).'</h1><p class="content-lede">'.$lede.'</p><div class="content-card">'.$html.'</div></main>';self::foot(); }
 public static function error(int $code,string $icon,string $title,string $desc):void{
  http_response_code($code);self::head("$title — ".self::SITE);
  echo '<main id="main-content" class="error-page"><div class="error-icon" aria-hidden="true">'.self::icon($icon).'</div><p class="error-code">Error '.$code.'</p><h1 class="error-title">'.self::e($title).'</h1><p class="error-desc">'.self::e($desc).'</p>';
  echo '<div class="error-actions"><a href="/index.php" class="btn btn-primary">'.self::icon('home').' Go to homepage</a> <a href="/contact.php" class="btn btn-secondary">'.self::icon('mail').' Contact us</a></div>';
  echo '<p class="error-links">Or try: <a href="/about.php">About</a> &middot; <a href="/privacy-policy.php">Privacy Policy</a> &middot; <a href="/terms-and-conditions.php">Terms &amp; Conditions</a></p></main>';self::foot(); }
}
