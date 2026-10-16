# QR & Barcode Generator

Part of the [TechZeeLand](https://github.com/TechZeeLand) family (Fonts, Icons, QR Codes, Assignment Cover Generator). Live at <https://qrcode.rayaz.org>.

A free, open-source, self-hosted **QR code and barcode generator**. Plain PHP, no framework, no database, no tracking. Codes are *static*: they encode your content directly, never a redirect through someone else's server.

## Features
- **QR codes:** versions 1-40, error correction L/M/Q/H, colours, **gradient**, module shapes (square, rounded, dots), eye shapes, **centre logo**, **frame + caption**, margin, size, transparent background
- **QR content helpers:** text/URL, Wi-Fi, email, phone, SMS, WhatsApp chat, location, calendar event, contact (vCard)
- **Barcodes:** Code 128, Code 39, EAN-13, EAN-8, UPC-A, ITF, ITF-14, Codabar, MSI Plessey, Pharmacode
- **Export:** SVG, PNG, copy PNG, copy SVG, **batch ZIP** (one value per line)
- **Productivity:** saved style presets and recent codes (browser only), live contrast/scannability hint
- **Public API:** `/api/qr.svg?data=...`, `/api/barcode.svg?type=...`, `/api/generate` (see `/docs`, downloadable as Markdown)
- **Admin portal** (`/admin`): dashboard with traffic and feature usage, generator defaults and limits, site and domain settings, header/footer/social link editor, editable policy pages, appearance (accent colour, logo, announcement, custom CSS), SEO, ads.txt and custom code, security (CORS, CSP hosts, maintenance mode), system health, ecosystem status, audit log, backup/restore, settings import/export
- **Private:** no visitor cookies, no analytics, anonymous aggregate counters only; strict Content-Security-Policy
- Uses the TechZeeLand **Icons** (`icons.<domain>/zi.css`) and **Fonts** (`fonts.<domain>/css2?family=Alata`) services

## Run it
```bash
cp .env.example .env
docker compose up -d --build      # http://localhost:1026/admin  (password is printed in `docker compose logs` on first start)
```
Without Docker (PHP 8.1+ with `zip`): `php -S localhost:8080 -t public public/index.php`

## Changing the domain
Everything derives from one value. Set `TZL_ROOT_DOMAIN=techzeeland.com` (or Admin > Site & domains > Root domain). The subdomains stay: `fonts.`, `icons.`, `qrcode.`, `assignmentcovergenerator.`. Also set `TZL_BASE_URL` (or the Public URL in Admin) to this service's own address.

## Layout
```
public/index.php     front controller (config in Kit::boot)
src/Tzl/             shared TZL Kit (identical in every PHP service; do not edit here, see tzl-kit/)
src/Web.php          routes of this service: generator, docs, API, admin page
src/Qr.php, Barcode.php, Render.php   encoders and SVG output
views/ resources/    generator view, API docs (Markdown)
```

## Not included yet
Data Matrix, PDF417, Aztec, Code 93, UPC-E, QR numeric/alphanumeric/Kanji modes. Test printed codes with a real scanner. Pull requests welcome.

## License
[GNU AGPL-3.0](LICENSE). If you run a modified version as a network service, offer its source to your users.


## Check a deployment
```bash
docker compose exec tzl-qr php bin/selftest.php
```
Lints every PHP file and requests the key pages; every line should say `ok`. The shared code in `src/Tzl/` comes from the separate TZL Kit repo (see its GUIDE.md); do not edit it here.
