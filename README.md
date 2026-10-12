# QR & Barcode Generator

Official repo: [TechZeeLand/QR-Barcode-Generator](https://github.com/TechZeeLand/QR-Barcode-Generator)

A free, open-source, self-hosted **QR code and barcode generator**. Plain PHP, no framework, no database, no tracking. Codes are *static*: they encode your content directly, never a redirect through someone else's server.

## Features
- **QR codes:** versions 1–40, error correction L/M/Q/H, foreground/background/eye colours, **gradient**, module shapes (square, rounded, dots), eye shapes (square, rounded, circle), **centre logo**, **frame + caption**, margin, size, transparent background
- **QR content helpers:** text/URL, Wi-Fi, email, phone, SMS, location, contact (vCard)
- **Barcodes:** Code 128 (auto set B/C), Code 39, EAN-13, EAN-8, UPC-A, Interleaved 2 of 5, ITF-14, Codabar, MSI Plessey, Pharmacode (check digits added automatically where defined)
- **Export:** SVG, PNG, copy to clipboard
- **Private:** no accounts, cookies, analytics, ads or third-party requests; strict Content-Security-Policy; nothing stored
- Pages: About, Contact, Privacy Policy, Terms & Conditions, Cookie Policy, 403/404/500; light/dark theme; inline SVG icon set in `public/assets/icons`

## Run it
```bash
docker compose up -d --build      # http://localhost:1026
```
Without Docker (PHP ≥ 8.1): `php -S localhost:8080 -t public`

Pre-built image (after pushing to GitHub, see `.github/workflows`): `ghcr.io/techzeeland/qr-barcode-generator:latest`. In Portainer: *Stacks → Add stack*, paste `docker-compose.yml` (swap `build: .` for `image: ghcr.io/techzeeland/qr-barcode-generator:latest`).

## Publish to GitHub
```bash
git init -b main && git add . && git commit -m "Initial commit"
git remote add origin https://github.com/TechZeeLand/QR-Barcode-Generator.git
git push -u origin main
```

## Project layout
```
src/Qr.php        QR encoder (own implementation, byte mode)
src/Barcode.php   1D barcode encoders
src/Render.php    SVG output and styling
src/Layout.php    shared header/footer/pages, inline icons
public/           index.php, api.php, pages, assets/{css,js,img,icons}
```

## Not included yet
Data Matrix, PDF417, Aztec, Code 93, UPC-E, QR numeric/alphanumeric/Kanji modes. Test printed codes with a real scanner. Pull requests welcome.

## License
[GNU AGPL-3.0](LICENSE). If you run a modified version as a network service, offer its source to your users.

Made by [TechZeeLand](https://github.com/TechZeeLand) · azlanaziz@rayaz.org
