SVG ICONS - Assignment Cover Generator edition
(started from the Online Store icon set; extended and repaired for this project)

ui/     line icons, 24x24 grid, stroke="currentColor" (inherits text colour), stroke-width 2, round caps/joins
social/ brand glyphs, filled with currentColor (google.svg keeps its official colours; signal.svg uses a 160x160 viewBox; github.svg uses a 16x16 viewBox)
other/  project-specific artwork (store favicon + image placeholder, these have fixed colours)

Resize: change width/height on the <svg> or size it in CSS. Recolour: set `color` on the element or parent.
In the PHP project they are inlined with  <?php echo \App\Icon::svg('name'); ?>  (see src/Icon.php).

[ui]
  ad.svg
  alert-circle.svg
  alert-triangle.svg
  arrow-left.svg
  arrow-right.svg
  arrow-up.svg
  award.svg
  book-open.svg
  car-front.svg
  check-circle.svg
  check.svg
  chevron-down.svg
  chevron-left.svg
  chevron-right.svg
  chevron-up.svg
  close.svg
  copy.svg
  credit-card.svg
  dashboard.svg
  download.svg
  external-link.svg
  eye-off.svg
  eye.svg
  file-log.svg
  file-pdf.svg
  file-text.svg
  folder.svg
  font.svg
  globe.svg
  graduation-cap.svg
  grid.svg
  hammer.svg
  heading.svg
  heart-filled.svg
  heart.svg
  home.svg
  id-card.svg
  image.svg
  info.svg
  key.svg
  link.svg
  loader.svg
  lock.svg
  logout.svg
  mail.svg
  map-pin-address.svg
  map-pin.svg
  menu.svg
  minimize.svg
  minus.svg
  moon.svg
  package.svg
  palette.svg
  paperclip.svg
  phone.svg
  play.svg
  plus-circle.svg
  plus.svg
  rotate-ccw.svg
  rotate-cw.svg
  save.svg
  search.svg
  server.svg
  settings.svg
  shield-check.svg
  shield.svg
  shopping-cart.svg
  signpost.svg
  sliders.svg
  snowflake-off.svg
  snowflake.svg
  square-pen.svg
  star-filled.svg
  star-outline.svg
  sun.svg
  tag.svg
  text-size.svg
  ticket.svg
  trash.svg
  truck.svg
  upload.svg
  user-lock.svg
  user.svg
  users-group.svg
  users.svg
  wrench.svg
  x-circle.svg

[social]
  facebook.svg
  github.svg
  google.svg
  instagram.svg
  messenger.svg
  signal.svg
  tiktok.svg
  whatsapp.svg
  youtube.svg

[other]
  image-placeholder.svg
  store-favicon.svg

Changes in this version
  Added  (ui):     ad, alert-circle, arrow-left, arrow-right, arrow-up, book-open, chevron-down, chevron-left, chevron-up,
                   copy, download, external-link, eye, eye-off, file-pdf, font, globe, graduation-cap, hammer, heading,
                   loader, lock, menu, minimize, minus, plus, plus-circle, rotate-ccw, rotate-cw, save, server, signpost,
                   sliders, square-pen, text-size, trash, upload, user-lock, users-group, x-circle
  Added  (social): github
  Fixed:           settings (gear outline was malformed), key (overlapping paths), car-front (redrawn),
                   preview.html (image-placeholder was written unclosed and swallowed signal.svg),
                   README (car-front was listed twice)

[added for QR-Barcode-Generator]
  ui/qr-code.svg  ui/barcode.svg  ui/scan.svg  ui/wifi.svg  ui/message-square.svg
