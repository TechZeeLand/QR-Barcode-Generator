# QR & Barcode Generator

A free, open-source, self-hosted **QR code and barcode generator**. Plain PHP, no framework, no database, no tracking. Codes are *static*: they encode your content directly, never a redirect through someone else's server.

## Features
- **QR codes** (versions 1–40, error correction L/M/Q/H) with custom colours, module shapes (square / rounded / dots), eye shapes, eye colour, margin, size and transparent background
- **Content helpers:** text/URL, Wi-Fi, email, phone, SMS, location
- **Barcodes:** Code 128 (set B), Code 39, EAN-13, UPC-A (check digits added automatically)
- **Export:** SVG, PNG, copy to clipboard
- **Private:** no accounts, cookies, analytics, ads or third-party requests; strict Content-Security-Policy
- Light/dark theme following your system, inline SVG icons (`public/assets/icons`)

## Run it
```bash
docker compose up -d --build   # http://localhost:1026
```
Or without Docker (PHP ≥ 8.1): `php -S localhost:8080 -t public`

## Layout
```
src/Qr.php        QR encoder (own implementation)
src/Barcode.php   1D barcode encoders
src/Render.php    SVG output and styling
public/           index.php, api.php, privacy.php, terms.php, assets/
```

## Known limits / roadmap
Not yet included: logo embedding, gradients, frames/captions, Data Matrix, PDF417, Aztec, ITF, Codabar, Code 128 subsets A/C, QR numeric/alphanumeric/Kanji modes. Always test printed codes with a real scanner. Pull requests welcome.

## License
[GNU AGPL-3.0](LICENSE). If you run a modified version as a network service, offer its source to your users.

Made by [TechZeeLand](https://github.com/TechZeeLand) · Contact: azlanaziz@rayaz.org
# QR-Barcode-Generator
# QR-Barcode-Generator
