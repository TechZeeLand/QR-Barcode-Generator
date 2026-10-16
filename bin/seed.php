<?php
/** First-run setup (safe to run any time): creates the admin account and prints the generated password once. */
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../src/Tzl/Kit.php';
require __DIR__ . '/../src/Web.php';
Tzl\Kit::boot(['root' => dirname(__DIR__), 'service' => 'qr', 'name' => 'QR & Barcode Generator', 'defaults' => App\Web::defaults()]);
$pw = Tzl\Auth::ensureAdmin();
if ($pw !== null) {
    $a = Tzl\Auth::admin();
    fwrite(STDOUT, "\n==============================================================\n"
        . " QR & Barcode Generator first run\n"
        . "   Admin user    : " . ($a['username'] ?? 'admin') . "\n"
        . "   Admin password: " . $pw . "\n"
        . " You will be asked to change it after signing in at /admin\n"
        . "==============================================================\n\n");
}
