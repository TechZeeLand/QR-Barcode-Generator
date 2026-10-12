<?php
declare(strict_types=1);require __DIR__.'/../src/Layout.php';
App\Layout::page('About','About the free, open-source QR & Barcode Generator.','About QR & Barcode Generator','A small, focused tool that turns text into clean, scannable codes.','
<h2>What it is</h2><p>QR &amp; Barcode Generator creates fully customizable QR codes (colours, gradients, module and eye shapes, logo, frame and caption) and common 1D barcodes: Code 128, Code 39, EAN-13, EAN-8, UPC-A, ITF, ITF-14, Codabar, MSI and Pharmacode. Download as SVG or PNG.</p>
<h2>Why it exists</h2><p>Many free generators quietly encode a redirect through their own servers, so scans are tracked and codes stop working if you stop paying. Codes made here are <strong>static</strong>: they contain your content directly, and nothing is stored.</p>
<h2>Open source &amp; self-hosted</h2><p>Released under the AGPL-3.0. Run your own copy with Docker; source, issues and contributions are on <a href="https://github.com/TechZeeLand/QR-Barcode-Generator" target="_blank" rel="noopener">GitHub</a>.</p>
<h2>Made in Bangladesh</h2><p>Built and maintained by <a href="https://github.com/TechZeeLand" target="_blank" rel="noopener">TechZeeLand</a>, based in Bangladesh.</p>');
