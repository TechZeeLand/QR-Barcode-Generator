<?php
use Tzl\Kit;
use App\Barcode;
/** @var array $cfg */
$e = static fn($s) => Kit::e($s);
?>
<main id="main-content" class="gen" data-cfg="<?= $e(json_encode($cfg)) ?>">
<div class="gen-intro">
  <h1><span class="grad">QR codes &amp; barcodes</span>, made yours</h1>
  <p>Colours, gradients, shapes, logos and frames. Static codes: your content is encoded directly and never tracked.</p>
</div>
<div class="gen-layout">
<form id="f" autocomplete="off" class="gen-form">
 <section class="card"><h2><?= Kit::icon('qr-code') ?> Content</h2>
  <div class="field-row">
   <div class="field"><label for="type">Code type</label><select name="type" id="type"><option value="qr">QR code</option><?php if (!empty($cfg['barcodes'])) { foreach (Barcode::TYPES as $k => $v) { echo '<option value="' . $e($k) . '">' . $e($v) . '</option>'; } } ?></select></div>
   <div class="field" id="qrOnly"><label for="kind">QR content</label><select id="kind"><option value="text">Text / URL</option><option value="wifi">Wi-Fi</option><option value="email">Email</option><option value="phone">Phone</option><option value="sms">SMS</option><option value="whatsapp">WhatsApp chat</option><option value="geo">Location</option><option value="event">Calendar event</option><option value="vcard">Contact (vCard)</option></select></div>
  </div>
  <div class="kind" data-k="text"><div class="field"><label for="v_text" id="v_label">Text or URL</label><textarea id="v_text" rows="3" placeholder="https://example.com"></textarea><p class="hint" id="v_hint"></p></div></div>
  <div class="kind" data-k="wifi" hidden><div class="field-row"><div class="field"><label for="w_ssid">Network name</label><input id="w_ssid"></div><div class="field"><label for="w_pass">Password</label><input id="w_pass"></div><div class="field"><label for="w_sec">Security</label><select id="w_sec"><option>WPA</option><option>WEP</option><option value="nopass">None</option></select></div></div><label class="row-check"><input type="checkbox" id="w_hid"><span class="switch"></span><span class="row-check-label">Hidden network</span></label></div>
  <div class="kind" data-k="email" hidden><div class="field"><label for="e_to">Address</label><input id="e_to" type="email"></div><div class="field"><label for="e_sub">Subject</label><input id="e_sub"></div><div class="field"><label for="e_body">Body</label><textarea id="e_body" rows="2"></textarea></div></div>
  <div class="kind" data-k="phone" hidden><div class="field"><label for="p_num">Number</label><input id="p_num" placeholder="+8801..."></div></div>
  <div class="kind" data-k="sms" hidden><div class="field"><label for="s_num">Number</label><input id="s_num"></div><div class="field"><label for="s_msg">Message</label><textarea id="s_msg" rows="2"></textarea></div></div>
  <div class="kind" data-k="whatsapp" hidden><div class="field"><label for="wa_num">Number with country code</label><input id="wa_num" placeholder="8801XXXXXXXXX"></div><div class="field"><label for="wa_msg">Pre-filled message (optional)</label><textarea id="wa_msg" rows="2"></textarea></div></div>
  <div class="kind" data-k="geo" hidden><div class="field-row"><div class="field"><label for="g_lat">Latitude</label><input id="g_lat" placeholder="23.8103"></div><div class="field"><label for="g_lng">Longitude</label><input id="g_lng" placeholder="90.4125"></div></div></div>
  <div class="kind" data-k="event" hidden><div class="field"><label for="ev_title">Title</label><input id="ev_title"></div><div class="field-row"><div class="field"><label for="ev_start">Starts</label><input id="ev_start" type="datetime-local"></div><div class="field"><label for="ev_end">Ends</label><input id="ev_end" type="datetime-local"></div></div><div class="field"><label for="ev_loc">Location</label><input id="ev_loc"></div></div>
  <div class="kind" data-k="vcard" hidden><div class="field-row"><div class="field"><label for="c_name">Full name</label><input id="c_name"></div><div class="field"><label for="c_org">Organization</label><input id="c_org"></div><div class="field"><label for="c_tel">Phone</label><input id="c_tel"></div><div class="field"><label for="c_mail">Email</label><input id="c_mail" type="email"></div><div class="field"><label for="c_url">Website</label><input id="c_url"></div></div></div>
  <input type="hidden" name="data" id="data"><input type="hidden" name="logo" id="logo">
 </section>

 <section class="card"><h2><?= Kit::icon('palette') ?> Colours</h2>
  <div class="field-row">
   <div class="field"><label for="fg">Foreground</label><input type="color" id="fg" name="fg" value="<?= $e($cfg['fg']) ?>"></div>
   <div class="field"><label for="bg">Background</label><input type="color" id="bg" name="bg" value="<?= $e($cfg['bg']) ?>"></div>
   <div class="field qr"><label for="eye">Eye colour</label><input type="color" id="eye" name="eye" value="<?= $e($cfg['fg']) ?>"></div>
   <div class="field qr" id="g2" hidden><label for="fg2">Gradient end</label><input type="color" id="fg2" name="fg2" value="#3fb4d4"></div>
  </div>
  <div class="switch-list">
   <label class="row-check qr"><input type="checkbox" name="grad" value="1" id="grad"><span class="switch"></span><span class="row-check-label">Gradient foreground</span></label>
   <label class="row-check"><input type="checkbox" name="transparent" value="1" id="transparent"><span class="switch"></span><span class="row-check-label">Transparent background</span></label>
  </div>
  <p class="contrast" id="contrast" role="status"></p>
 </section>

 <section class="card qr"><h2><?= Kit::icon('sliders') ?> QR style</h2>
  <div class="field-row">
   <div class="field"><label for="shape">Modules</label><select id="shape" name="shape"><option value="square">Square</option><option value="rounded">Rounded</option><option value="dot">Dots</option></select></div>
   <div class="field"><label for="eyeShape">Eyes</label><select id="eyeShape" name="eyeShape"><option value="square">Square</option><option value="rounded">Rounded</option><option value="circle">Circle</option></select></div>
   <div class="field"><label for="ecc">Error correction</label><select id="ecc" name="ecc"><?php foreach (['L' => 'Low (7%)', 'M' => 'Medium (15%)', 'Q' => 'Quartile (25%)', 'H' => 'High (30%)'] as $k => $v) { echo '<option value="' . $k . '"' . ($k === $cfg['ecc'] ? ' selected' : '') . '>' . $v . '</option>'; } ?></select></div>
  </div>
  <div class="field-row">
   <div class="field"><label for="frame">Frame</label><select id="frame" name="frame"><option value="">None</option><option value="square">Square</option><option value="rounded">Rounded</option></select></div>
   <div class="field"><label for="caption">Caption</label><input id="caption" name="caption" maxlength="40" placeholder="Scan me"></div>
  </div>
  <?php if (!empty($cfg['logo'])): ?>
  <div class="field"><label for="logofile">Logo in the centre</label><input type="file" id="logofile" accept="image/png,image/jpeg,image/webp,image/svg+xml"><button type="button" class="btn btn-secondary btn-sm" id="logoclr" hidden><?= Kit::icon('trash') ?> Remove logo</button><p class="hint">Adding a logo switches error correction to High so the code stays scannable. Max 300 KB.</p></div>
  <?php endif; ?>
 </section>

 <section class="card"><h2><?= Kit::icon('image') ?> Size</h2>
  <div class="field-row">
   <div class="field"><label for="size">Size (px)</label><input type="number" id="size" name="size" value="<?= (int)$cfg['size'] ?>" min="100" max="2000"></div>
   <div class="field"><label for="margin" id="margin_label">Margin (modules)</label><input type="number" id="margin" name="margin" value="<?= (int)$cfg['margin'] ?>" min="0" max="20"></div>
  </div>
  <label class="row-check bar" hidden><input type="checkbox" name="text" value="1" checked><span class="switch"></span><span class="row-check-label">Show text under the bars</span></label>
 </section>

 <section class="card"><h2><?= Kit::icon('bookmark') ?> Presets</h2>
  <div class="preset-row"><input type="text" id="preset-name" maxlength="30" placeholder="Preset name" aria-label="Preset name"><button type="button" class="btn btn-secondary" id="preset-save"><?= Kit::icon('save') ?> Save style</button><button type="button" class="btn btn-secondary" id="reset"><?= Kit::icon('rotate-ccw') ?> Reset</button></div>
  <div id="presets" class="chips"></div>
  <p class="hint">Presets store colours and shapes in this browser only (not on the server).</p>
 </section>

 <?php if (!empty($cfg['batch'])): ?>
 <section class="card"><h2><?= Kit::icon('package') ?> Batch</h2>
  <p class="hint">One value per line (up to <?= (int)$cfg['batchMax'] ?>). Uses the style and code type above and downloads a ZIP of SVG files.</p>
  <div class="field"><label for="lines" class="sr-only">Lines</label><textarea id="lines" rows="4" placeholder="https://example.com/a&#10;https://example.com/b"></textarea></div>
  <button type="button" class="btn btn-secondary" id="batch"><?= Kit::icon('download') ?> Download ZIP</button> <span class="hint" id="batch-msg" role="status"></span>
 </section>
 <?php endif; ?>
</form>

<aside class="gen-side">
 <section class="card"><h2><?= Kit::icon('scan') ?> Preview</h2>
  <div id="preview"><img id="img" alt="Generated code preview" hidden><p id="msg" class="hint">Start typing to see your code.</p></div>
  <div class="btn-row">
   <button type="button" class="btn btn-primary" id="dsvg"><?= Kit::icon('download') ?> SVG</button>
   <button type="button" class="btn btn-secondary" id="dpng"><?= Kit::icon('image') ?> PNG</button>
   <button type="button" class="btn btn-secondary" id="cp"><?= Kit::icon('copy') ?> <span>Copy PNG</span></button>
   <button type="button" class="btn btn-secondary" id="cpsvg"><?= Kit::icon('code') ?> <span>Copy SVG</span></button>
  </div>
  <p class="hint"><?= Kit::icon('shield-check') ?> Static codes: your content is encoded directly, never redirected. Nothing is stored. Always test a printed code with a real scanner.</p>
 </section>
 <section class="card"><h2><?= Kit::icon('link') ?> Use it from your own page</h2>
  <p class="hint">Same code as a URL, ready for an <code>&lt;img&gt;</code> tag:</p>
  <div class="code-block" data-lang="url"><pre><code id="apiurl">…</code></pre></div>
  <p class="hint"><a href="/docs">Read the API documentation</a></p>
 </section>
 <section class="card"><h2><?= Kit::icon('history') ?> Recent</h2>
  <div id="history" class="history-grid"></div>
  <p class="hint" id="history-empty">Codes you download appear here (stored in this browser only).</p>
 </section>
</aside>
</div>
</main>
