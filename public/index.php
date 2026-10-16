<?php
declare(strict_types=1);
/**
 * QR & Barcode Generator front controller. Every request that is not a real file lands here
 * (Apache FallbackResource, or `php -S localhost:8080 -t public public/index.php`).
 */
require __DIR__ . '/../src/Tzl/Kit.php';
require __DIR__ . '/../src/Web.php'; // needed before boot() because its defaults feed the config

use Tzl\Kit;

Kit::boot([
    'root'        => dirname(__DIR__),
    'service'     => 'qr',
    'version'     => '2.0.0',
    'name'        => 'QR & Barcode Generator',
    'short'       => 'QR Generator',
    'tagline'     => 'Free, open-source QR codes and barcodes. Static codes, no tracking, nothing stored.',
    'description' => 'Free, open-source QR code and barcode generator. Customize colours, shapes and logos, then download SVG or PNG. No tracking, nothing stored.',
    'github'      => 'https://github.com/TechZeeLand/QR-Barcode-Generator',
    'css'         => ['assets/css/app.css'],
    'nav'         => [['Generator', '/', 'qr-code'], ['API & docs', '/docs', 'book-open'], ['About', '/about', 'info']],
    'footer_extra' => [['API & docs', '/docs']],
    'sitemap'     => ['/', '/docs'],
    'robots_disallow' => ['/api/'],
    'defaults'    => App\Web::defaults(),
    'admin_menu'  => ['qr' => ['/admin/qr', 'Generator settings', 'qr-code']],
    'dashboard'   => [App\Web::class, 'dashboard'],
    'page_extra'  => [
        'privacy-policy' => "\n- **Your content.** While you edit, the text, style options and (if you add one) your logo image are sent to this server, which draws the code and returns it. Nothing is written to a database, file or log. PNG downloads are produced in your browser.\n- **Static codes.** Codes encode your content directly and never point to our servers, so scans cannot be tracked and codes keep working if this site disappears.",
    ],
]);

if (PHP_SAPI === 'cli-server') {
    $p = parse_url((string)($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
    if ($p !== '/' && is_file(__DIR__ . $p) && substr((string)$p, -4) !== '.php') { return false; }
}

Tzl\Core::run([App\Web::class, 'register']);
