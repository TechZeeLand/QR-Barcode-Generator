<?php
declare(strict_types=1);require __DIR__.'/../src/Layout.php';
App\Layout::page('Privacy Policy','How QR & Barcode Generator handles your data: it stores nothing.','Privacy Policy','Last updated: '.date('F Y'),'
<h2>Short version</h2><p>No accounts, no cookies, no analytics, no ads. What you type is used to draw your code and then forgotten.</p>
<h2>What happens to your content</h2><p>While you edit, the text, style options and (if you add one) your logo image are sent to this server, which draws the code and returns it. The app does not write any of it to a database, file or log. PNG and SVG downloads are produced in your browser.</p>
<h2>What we don\'t collect</h2><p>No personal data, no fingerprinting, no third-party scripts, fonts or trackers. Pages load only files from their own origin.</p>
<h2>Server logs</h2><p>Whoever hosts an instance (a web host or reverse proxy) may keep ordinary access logs such as IP address and time. The app does not add request contents to them. If you self-host, you control this.</p>
<h2>Your codes</h2><p>Codes are static: they encode your content directly and never point to our servers, so we can\'t track scans and they keep working if this site disappears.</p>
<h2>Your rights</h2><p>Because we hold no personal data, there is nothing to access or delete. Questions? <a href="/contact.php">Contact us</a>.</p>
<h2>Changes</h2><p>We\'ll update this page if data handling changes.</p>');
