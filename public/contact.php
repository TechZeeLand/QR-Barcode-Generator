<?php
declare(strict_types=1);require __DIR__.'/../src/Layout.php';use App\Layout as L;
$li=fn($i,$u,$t)=>'<li><a href="'.$u.'" target="_blank" rel="noopener">'.L::icon($i).' '.$t.'</a></li>';
L::page('Contact','Get in touch with TechZeeLand, the team behind QR & Barcode Generator.','Contact','We\'d love to hear from you.','
<p>For bugs or feature requests, opening an issue on GitHub is the fastest way to reach us. For anything else, reach out by email or on social media.</p><ul class="contact-list">
<li><a href="mailto:azlanaziz@rayaz.org">'.L::icon('mail').' azlanaziz@rayaz.org</a></li>'.
$li('github','https://github.com/TechZeeLand/QR-Barcode-Generator','Open an issue on GitHub').$li('youtube','https://www.youtube.com/@TechZeeLand','YouTube — @TechZeeLand').$li('facebook','https://www.facebook.com/TechZeeLand','Facebook — TechZeeLand').$li('instagram','https://www.instagram.com/TechZeeLand','Instagram — @TechZeeLand').$li('tiktok','https://www.tiktok.com/@TechZeeLand','TikTok — @TechZeeLand').'</ul>');
