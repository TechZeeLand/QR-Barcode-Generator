<?php
declare(strict_types=1);require __DIR__.'/../src/Layout.php';
App\Layout::page('Cookie Policy','Which cookies and browser storage QR & Barcode Generator uses.','Cookie Policy','Last updated: '.date('F Y'),'
<h2>Short version</h2><p>This site sets <strong>no cookies</strong>. It uses no analytics, advertising or tracking scripts.</p>
<h2>Browser storage</h2><table class="policy-table"><thead><tr><th>Name</th><th>Type</th><th>Purpose</th><th>Lifetime</th></tr></thead><tbody>
<tr><td><code>qbg-theme</code></td><td>localStorage (not a cookie)</td><td>Remembers your light/dark choice if you use the theme toggle. Never sent to the server.</td><td>Until you clear it</td></tr></tbody></table>
<h2>Control</h2><p>Clear site data in your browser to remove it. Nothing else is stored.</p>');
