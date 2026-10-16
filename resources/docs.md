## Overview

{site} draws QR codes and barcodes as SVG on the server. You can use the web page, or call it from your own site with plain URLs. There is no key and no sign-up. Requests are rate-limited per visitor.

Base URL: `{base}`

## QR code as an image

```html
<img src="{base}/api/qr.svg?data=https%3A%2F%2Fexample.com&fg=%23000000&bg=%23ffffff&size=300" alt="QR code">
```

| Parameter | Meaning | Default |
|---|---|---|
| `data` | Content to encode (URL-encoded, required) | |
| `ecc` | Error correction: `L`, `M`, `Q`, `H` | server default |
| `fg`, `bg` | Colours as `#rrggbb` | `#000000`, `#ffffff` |
| `eye` | Colour of the three corner eyes | same as `fg` |
| `grad`, `fg2` | `grad=1` turns on a gradient from `fg` to `fg2` | off |
| `shape` | Modules: `square`, `rounded`, `dot` | `square` |
| `eyeShape` | Eyes: `square`, `rounded`, `circle` | `square` |
| `frame`, `caption` | Frame `square` or `rounded` and a caption (max 40 characters) | none |
| `margin` | Quiet zone in modules (0 to 10) | 4 |
| `size` | Width in pixels (100 to 2000) | 400 |
| `transparent` | `1` for a transparent background | off |

Responses are cached for a day and carry an `ETag`.

## Barcode as an image

```html
<img src="{base}/api/barcode.svg?type=code128&data=HELLO-123&size=360" alt="Barcode">
```

Supported `type` values: {types}. Check digits are added automatically where the format defines one. Extra parameters: `fg`, `bg`, `margin` (quiet zone, default 10), `size`, `text=1` to print the value under the bars, `transparent=1`.

## From JavaScript

```js
const res = await fetch('{base}/api/generate', {
  method: 'POST',
  body: new URLSearchParams({ type: 'qr', data: 'https://example.com', fg: '#0f7a63' })
});
const svg = await res.text();
```

On error the response has a 4xx status and a plain-text message.

## Batch

`POST {base}/api/batch` with `lines` (one value per line) plus the same style fields returns a ZIP of SVG files. Failed lines are listed in `errors.txt`.

## Limits and fair use

Content is limited to a few thousand characters, batches to a set number of lines, and each visitor to a number of requests per minute. If you need more, self-host: the project is open source.

## Static codes

Codes are static. They encode your content directly and never redirect through this server, so they cannot be tracked and keep working forever.
