<?php
declare(strict_types=1);
namespace App;
final class Layout {
 public static function icon(string $n):string{ foreach(['ui','social','other'] as $d){$f=__DIR__."/../public/assets/icons/$d/$n.svg";if(is_file($f))return (string)file_get_contents($f);}return ''; }
 public static function head(string $title,string $desc=''):void{ $t=htmlspecialchars($title);$d=htmlspecialchars($desc?:'Free, open-source QR code and barcode generator. No tracking, nothing stored.');
  echo "<!doctype html><html lang=\"en\"><head><meta charset=\"utf-8\"><meta name=\"viewport\" content=\"width=device-width,initial-scale=1\"><title>$t</title><meta name=\"description\" content=\"$d\"><link rel=\"icon\" href=\"/favicon.ico\"><link rel=\"stylesheet\" href=\"/assets/css/style.css\"></head><body>";
  echo '<header class="top"><a class="brand" href="/">'.self::icon('qr-code').' QR &amp; Barcode Generator</a><nav><a href="/privacy.php">Privacy</a><a href="/terms.php">Terms</a><a href="https://github.com/TechZeeLand/QR-Barcode-Generator" rel="noopener">'.self::icon('github').' GitHub</a></nav></header>'; }
 public static function foot():void{
  $s=['youtube'=>'https://www.youtube.com/@TechZeeLand','facebook'=>'https://www.facebook.com/TechZeeLand','instagram'=>'https://www.instagram.com/TechZeeLand','tiktok'=>'https://www.tiktok.com/@TechZeeLand','github'=>'https://github.com/TechZeeLand'];
  echo '<footer class="foot"><div class="soc">';foreach($s as $k=>$u)echo "<a href=\"$u\" target=\"_blank\" rel=\"noopener\" aria-label=\"$k\">".self::icon($k).'</a>';
  echo '</div><p>&copy; '.date('Y').' TechZeeLand. Free &amp; open source under the <a href="https://www.gnu.org/licenses/agpl-3.0.html" rel="noopener">AGPL-3.0</a>. <a href="mailto:azlanaziz@rayaz.org">Contact</a></p></footer></body></html>'; }
}
