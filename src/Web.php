<?php
declare(strict_types=1);
namespace App;

use Tzl\Admin;
use Tzl\Auth;
use Tzl\Json;
use Tzl\Kit;
use Tzl\Layout;
use Tzl\Markdown;
use Tzl\Router;
use Tzl\Site;
use Tzl\Stats;
use Tzl\Ui;

/** Routes specific to the QR & Barcode Generator. Shared pages, /admin and /health come from the TZL Kit. */
final class Web
{
    /** Style parameters accepted by the API and the UI (everything else is ignored). */
    private const STYLE_KEYS = ['fg', 'bg', 'eye', 'fg2', 'grad', 'shape', 'eyeShape', 'frame', 'caption', 'margin', 'size', 'transparent', 'text', 'logo'];

    public static function defaults(): array
    {
        return [
            'qr_size' => 400, 'qr_margin' => 4, 'qr_ecc' => 'M', 'qr_fg' => '#000000', 'qr_bg' => '#ffffff',
            'qr_max_len' => 2000, 'qr_allow_logo' => true, 'qr_batch_on' => true, 'qr_batch_max' => 100,
            'qr_api_on' => true, 'qr_api_rate' => 120, 'qr_barcodes_on' => true,
        ];
    }

    public static function register(): void
    {
        Router::get('#^/$#', [self::class, 'home']);
        Router::get('#^/docs$#', [self::class, 'docs']);
        Router::get('#^/docs\.md$#', [self::class, 'docsMd']);
        Router::get('#^/api/qr\.svg$#', [self::class, 'apiQr']);
        Router::get('#^/api/barcode\.svg$#', [self::class, 'apiBarcode']);
        Router::post('#^/api/generate$#', [self::class, 'generate']);
        Router::post('#^/api/batch$#', [self::class, 'batch']);
        Router::get('#^/admin/qr$#', [self::class, 'adminQr']);
        Router::post('#^/admin/qr$#', [self::class, 'adminQrSave']);
    }

    /* ---------- pages ---------- */

    public static function home(): void
    {
        $s = Site::all();
        Layout::head('QR Code & Barcode Generator', 'Free, open-source QR code and barcode generator. Customize colours, shapes and logos, then download SVG or PNG. No tracking, nothing stored.', [
            'csp' => ['img-src' => 'blob:'], 'body' => 'page-gen',
        ]);
        $cfg = [
            'size' => (int)$s['qr_size'], 'margin' => (int)$s['qr_margin'], 'ecc' => (string)$s['qr_ecc'], 'fg' => (string)$s['qr_fg'], 'bg' => (string)$s['qr_bg'],
            'maxLen' => (int)$s['qr_max_len'], 'logo' => !empty($s['qr_allow_logo']), 'batch' => !empty($s['qr_batch_on']), 'batchMax' => (int)$s['qr_batch_max'], 'barcodes' => !empty($s['qr_barcodes_on']),
        ];
        require Kit::$root . '/views/generator.php';
        Layout::foot(['js' => ['assets/js/app.js']]);
    }

    private static function docsMarkdown(): string
    {
        $md = (string)@file_get_contents(Kit::$root . '/resources/docs.md');
        return strtr($md, ['{base}' => Kit::baseUrl(), '{site}' => (string)Site::get('site_name'), '{types}' => implode(', ', array_keys(Barcode::TYPES))]);
    }

    public static function docs(): void
    {
        $toc = [];
        $html = Markdown::render(self::docsMarkdown(), $toc);
        Layout::head('API & documentation', 'Use the QR & Barcode Generator from your own pages: URL parameters, JSON endpoints and examples.');
        echo '<main id="main-content" class="content-wrap wide"><h1>API &amp; documentation</h1><p class="content-lede">Generate codes from your own pages with plain URLs. No key, no sign-up.</p>';
        echo '<div class="docs-actions"><a class="btn btn-secondary" href="/docs.md" download="qr-generator-docs.md">' . Kit::icon('download') . ' Download as Markdown</a></div>';
        echo '<div class="content-card prose">' . $html . '</div></main>';
        Layout::foot();
    }

    public static function docsMd(): void
    {
        header('Content-Type: text/markdown; charset=utf-8');
        header('Content-Disposition: inline; filename="qr-generator-docs.md"');
        echo self::docsMarkdown();
        exit;
    }

    /* ---------- generation ---------- */

    /** Rate limit per client: N requests per minute for API-style endpoints. */
    private static function limit(): void
    {
        $max = (int)Site::get('qr_api_rate');
        if ($max <= 0) { return; }
        $f = 'ratelimit/' . substr(sha1(Kit::clientIp()), 0, 16) . '.json';
        $minute = (int)floor(time() / 60);
        $n = 0;
        try {
            Json::update($f, static function ($d) use ($minute, &$n) {
                $d = is_array($d) && ($d['m'] ?? 0) === $minute ? $d : ['m' => $minute, 'n' => 0];
                $d['n']++;
                $n = $d['n'];
                return $d;
            }, []);
        } catch (\Throwable $e) {
            return;
        }
        if (random_int(1, 200) === 1) { self::sweepLimits($minute); }
        if ($n > $max) {
            Kit::cors();
            header('Retry-After: 60');
            http_response_code(429);
            header('Content-Type: text/plain; charset=utf-8');
            echo 'Rate limit exceeded. Try again in a minute.';
            exit;
        }
    }

    private static function sweepLimits(int $minute): void
    {
        foreach (glob(Json::path('ratelimit/*.json')) ?: [] as $f) {
            $d = json_decode((string)@file_get_contents($f), true);
            if (!is_array($d) || ($d['m'] ?? 0) < $minute - 5) { @unlink($f); }
        }
    }

    private static function fail(string $msg, int $code = 400): void
    {
        Kit::cors();
        http_response_code($code);
        header('Content-Type: text/plain; charset=utf-8');
        header('Cache-Control: no-store');
        echo $msg;
        exit;
    }

    /** Build an SVG for $type ('qr' or a barcode type) from request parameters. Throws InvalidArgumentException on bad input. */
    public static function build(string $type, string $data, array $in): string
    {
        $max = max(10, min(2900, (int)Site::get('qr_max_len')));
        if (strlen($data) > $max) { throw new \InvalidArgumentException('Content is too long (max ' . $max . ' characters).'); }
        $o = [];
        foreach (self::STYLE_KEYS as $k) { if (isset($in[$k]) && is_scalar($in[$k])) { $o[$k] = (string)$in[$k]; } }
        $o += ['fg' => (string)Site::get('qr_fg'), 'bg' => (string)Site::get('qr_bg')];
        if ($type === 'qr') {
            $o += ['size' => (string)Site::get('qr_size'), 'margin' => (string)Site::get('qr_margin')];
            if (!Site::on('qr_allow_logo')) { unset($o['logo']); }
            $ecc = (string)($in['ecc'] ?? Site::get('qr_ecc'));
            if (Render::logo($o['logo'] ?? null) !== '') { $ecc = 'H'; }
            return Render::qr(Qr::encode($data, $ecc), $o);
        }
        if (!Site::on('qr_barcodes_on')) { throw new \InvalidArgumentException('Barcodes are switched off on this server.'); }
        $o += ['size' => '400', 'margin' => '10'];
        return Render::bar(Barcode::encode($type, $data), $o);
    }

    private static function svgOut(string $svg, string $kind, string $key): void
    {
        Stats::event($kind, $key);
        Kit::cors();
        header('Content-Type: image/svg+xml; charset=utf-8');
        header("Content-Security-Policy: default-src 'none'; style-src 'unsafe-inline'; sandbox");
        header('X-Content-Type-Options: nosniff');
        echo $svg;
        exit;
    }

    /** UI endpoint (POST form) used by the generator page. */
    public static function generate(): void
    {
        self::limit();
        $type = (string)($_POST['type'] ?? 'qr');
        $data = (string)($_POST['data'] ?? '');
        header('Cache-Control: no-store');
        try {
            if ($type !== 'qr' && !isset(Barcode::TYPES[$type])) { throw new \InvalidArgumentException('Unknown code type.'); }
            $svg = self::build($type, $data, $_POST);
        } catch (\InvalidArgumentException | \RuntimeException $e) {
            self::fail($e->getMessage());
            return;
        } catch (\Throwable $e) {
            error_log('generate: ' . $e->getMessage());
            self::fail('Could not generate this code.');
            return;
        }
        // The live preview fires on every keystroke; only explicit downloads/copies are counted (see app.js: ?count=1).
        if (!empty($_POST['count'])) { Stats::event($type === 'qr' ? 'qr' : 'barcode', $type); }
        Kit::cors();
        header('Content-Type: image/svg+xml; charset=utf-8');
        echo $svg;
        exit;
    }

    public static function apiQr(): void
    {
        if (!Site::on('qr_api_on')) { self::fail('The API is switched off on this server.', 403); }
        self::limit();
        $data = (string)($_GET['data'] ?? '');
        try {
            $svg = self::build('qr', $data, $_GET);
        } catch (\InvalidArgumentException | \RuntimeException $e) {
            self::fail($e->getMessage());
            return;
        }
        Kit::etag(substr(sha1($svg), 0, 20));
        header('Cache-Control: public, max-age=86400');
        self::svgOut($svg, 'api.qr', 'qr');
    }

    public static function apiBarcode(): void
    {
        if (!Site::on('qr_api_on')) { self::fail('The API is switched off on this server.', 403); }
        self::limit();
        $type = (string)($_GET['type'] ?? 'code128');
        if (!isset(Barcode::TYPES[$type])) { self::fail('Unknown barcode type. Use one of: ' . implode(', ', array_keys(Barcode::TYPES))); }
        try {
            $svg = self::build($type, (string)($_GET['data'] ?? ''), $_GET);
        } catch (\InvalidArgumentException | \RuntimeException $e) {
            self::fail($e->getMessage());
            return;
        }
        Kit::etag(substr(sha1($svg), 0, 20));
        header('Cache-Control: public, max-age=86400');
        self::svgOut($svg, 'api.barcode', $type);
    }

    /** One code per line -> ZIP of SVG files. */
    public static function batch(): void
    {
        if (!Site::on('qr_batch_on')) { self::fail('Batch generation is switched off on this server.', 403); }
        if (!class_exists('ZipArchive')) { self::fail('ZIP support is not installed on this server.', 501); }
        self::limit();
        $type = (string)($_POST['type'] ?? 'qr');
        if ($type !== 'qr' && !isset(Barcode::TYPES[$type])) { self::fail('Unknown code type.'); }
        $lines = preg_split('/\R/', (string)($_POST['lines'] ?? '')) ?: [];
        $lines = array_values(array_filter(array_map('trim', $lines), static fn($l) => $l !== ''));
        $maxN = max(1, min(500, (int)Site::get('qr_batch_max')));
        if (!$lines) { self::fail('Enter at least one line.'); }
        if (count($lines) > $maxN) { self::fail('Too many lines (max ' . $maxN . ').'); }
        $tmp = tempnam(sys_get_temp_dir(), 'tzlqr');
        $zip = new \ZipArchive();
        if ($tmp === false || $zip->open($tmp, \ZipArchive::OVERWRITE) !== true) { self::fail('Could not create the ZIP file.', 500); }
        $errs = [];
        foreach ($lines as $i => $line) {
            try {
                $name = sprintf('%03d-%s.svg', $i + 1, substr(trim((string)preg_replace('/[^a-z0-9]+/i', '-', $line), '-'), 0, 40) ?: 'code');
                $zip->addFromString($name, self::build($type, $line, $_POST));
            } catch (\InvalidArgumentException | \RuntimeException $e) {
                $errs[] = 'Line ' . ($i + 1) . ': ' . $e->getMessage();
            }
        }
        if ($errs) { $zip->addFromString('errors.txt', implode("\n", $errs) . "\n"); }
        $zip->close();
        Stats::event('batch', (string)count($lines));
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="codes-' . gmdate('Ymd-His') . '.zip"');
        header('Content-Length: ' . filesize($tmp));
        header('Cache-Control: no-store');
        readfile($tmp);
        @unlink($tmp);
        exit;
    }

    /* ---------- admin ---------- */

    public static function dashboard(array $days): array
    {
        $qr = Stats::sumEvent($days, 'qr') + Stats::sumEvent($days, 'api.qr');
        $bar = Stats::sumEvent($days, 'barcode') + Stats::sumEvent($days, 'api.barcode');
        $types = Stats::topEventKeys($days, 'barcode', 6);
        return [
            'cards' => [['qr-code', number_format($qr), 'QR codes, 14 days'], ['barcode', number_format($bar), 'barcodes, 14 days'], ['package', number_format(Stats::sumEvent($days, 'batch')), 'batch downloads, 14 days']],
            'html' => '<div class="admin-cols">' . Ui::card('Barcode types used', 'barcode', Ui::rank($types)) . Ui::card('Quick links', 'wrench', '<div class="tool-forms"><a class="btn btn-secondary" href="/admin/qr">' . Kit::icon('sliders') . ' Generator settings</a><a class="btn btn-secondary" href="/docs" target="_blank" rel="noopener">' . Kit::icon('book-open') . ' API docs</a></div>') . '</div>',
        ];
    }

    public static function adminQr(): void
    {
        $s = Site::all();
        $h = '<form method="post" action="/admin/qr" class="admin-form">' . Auth::field();
        $h .= Ui::card('Defaults for new codes', 'sliders',
            '<div class="field-row">' . Ui::text('Size (px)', 'qr_size', (string)$s['qr_size'], ['type' => 'number', 'min' => 100, 'max' => 2000, 'narrow' => true]) . Ui::text('Margin (modules)', 'qr_margin', (string)$s['qr_margin'], ['type' => 'number', 'min' => 0, 'max' => 20, 'narrow' => true])
            . Ui::select('Error correction', 'qr_ecc', ['L' => 'Low (7%)', 'M' => 'Medium (15%)', 'Q' => 'Quartile (25%)', 'H' => 'High (30%)'], (string)$s['qr_ecc']) . '</div>'
            . '<div class="field-row">' . Ui::color('Foreground', 'qr_fg', (string)$s['qr_fg']) . Ui::color('Background', 'qr_bg', (string)$s['qr_bg']) . '</div>'
            . '<p class="hint">These apply to the generator page and to the API when a parameter is omitted.</p>');
        $h .= Ui::card('Features', 'toggle-on',
            '<div class="switch-list">' . Ui::check('qr_allow_logo', 'Allow a centre logo on QR codes', !empty($s['qr_allow_logo']))
            . Ui::check('qr_barcodes_on', 'Offer 1D barcodes', !empty($s['qr_barcodes_on']))
            . Ui::check('qr_batch_on', 'Offer batch generation (ZIP of SVGs)', !empty($s['qr_batch_on']))
            . Ui::check('qr_api_on', 'Enable the public URL API (/api/qr.svg, /api/barcode.svg)', !empty($s['qr_api_on'])) . '</div>');
        $h .= Ui::card('Limits', 'gauge',
            '<div class="field-row">' . Ui::text('Max content length (characters)', 'qr_max_len', (string)$s['qr_max_len'], ['type' => 'number', 'min' => 10, 'max' => 2900, 'narrow' => true])
            . Ui::text('Max lines per batch', 'qr_batch_max', (string)$s['qr_batch_max'], ['type' => 'number', 'min' => 1, 'max' => 500, 'narrow' => true])
            . Ui::text('Requests per minute per visitor', 'qr_api_rate', (string)$s['qr_api_rate'], ['type' => 'number', 'min' => 0, 'max' => 10000, 'narrow' => true, 'hint' => '0 disables the limit. Applies to the preview, API and batch.']) . '</div>');
        $h .= '<div class="form-actions"><button class="btn btn-primary" type="submit">' . Kit::icon('save') . ' Save</button></div></form>';
        Admin::page('Generator settings', 'qr', $h);
    }

    public static function adminQrSave(): void
    {
        Auth::verifyCsrf();
        $int = static function (string $k, int $lo, int $hi, int $d): int {
            $v = Admin::str($k, 8);
            return max($lo, min($hi, is_numeric($v) ? (int)$v : $d));
        };
        $ecc = Admin::str('qr_ecc', 1);
        Site::save([
            'qr_size' => $int('qr_size', 100, 2000, 400), 'qr_margin' => $int('qr_margin', 0, 20, 4), 'qr_ecc' => in_array($ecc, ['L', 'M', 'Q', 'H'], true) ? $ecc : 'M',
            'qr_fg' => Site::cleanColor(Admin::str('qr_fg', 7), '#000000'), 'qr_bg' => Site::cleanColor(Admin::str('qr_bg', 7), '#ffffff'),
            'qr_max_len' => $int('qr_max_len', 10, 2900, 2000), 'qr_batch_max' => $int('qr_batch_max', 1, 500, 100), 'qr_api_rate' => $int('qr_api_rate', 0, 10000, 120),
            'qr_allow_logo' => Admin::bool('qr_allow_logo'), 'qr_barcodes_on' => Admin::bool('qr_barcodes_on'), 'qr_batch_on' => Admin::bool('qr_batch_on'), 'qr_api_on' => Admin::bool('qr_api_on'),
        ]);
        Json::log('Updated generator settings');
        Admin::flash('ok', 'Generator settings saved.');
        Kit::redirect('/admin/qr');
    }
}
