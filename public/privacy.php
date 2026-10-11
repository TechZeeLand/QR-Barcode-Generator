<?php
declare(strict_types=1);require __DIR__.'/../src/Layout.php';use App\Layout;
Layout::head('Privacy Policy — QR & Barcode Generator');?>
<main class="doc"><h1>Privacy Policy</h1><p class="muted">Last updated: October 2026</p>
<h2>Short version</h2><p>We don't ask for an account, set cookies, run analytics or show ads. What you type is used to draw your code and then forgotten.</p>
<h2>What happens to your content</h2><p>While you edit, the text and style options are sent to this server over HTTPS, which draws the code and returns an image. The app does not write that content to a database, file or log. Downloads (SVG, PNG) are created in your browser.</p>
<h2>What we don't collect</h2><p>No accounts, no cookies, no fingerprinting, no third-party scripts, fonts or trackers. The page loads only files from its own origin.</p>
<h2>Server logs</h2><p>Whoever hosts an instance (for example your web host or reverse proxy) may keep ordinary access logs such as IP address and time. The app itself doesn't add request bodies to them. If you self-host, you control this.</p>
<h2>Your QR codes</h2><p>Codes encode your content directly (static codes). They never point to our servers, can't be tracked by us, and keep working even if this site disappears.</p>
<h2>Changes &amp; contact</h2><p>We'll update this page if data handling changes. Questions: <a href="mailto:azlanaziz@rayaz.org">azlanaziz@rayaz.org</a>.</p></main>
<?php Layout::foot();
