<?php
declare(strict_types=1);require __DIR__.'/../src/Layout.php';require __DIR__.'/../src/Barcode.php';use App\Layout as L;
L::head('QR Code & Barcode Generator — free, private, open source');?>
<main id="main-content" class="gen">
<form id="f" autocomplete="off" class="gen-form">
 <section class="card"><h2><?php echo L::icon('qr-code'); ?> Content</h2>
  <div class="field"><label for="type">Code type</label><select name="type" id="type"><option value="qr">QR code</option><?php foreach(App\Barcode::TYPES as $k=>$v)echo "<option value=\"$k\">$v</option>"; ?></select></div>
  <div class="field" id="qrOnly"><label for="kind">QR content</label><select id="kind"><option value="text">Text / URL</option><option value="wifi">Wi-Fi</option><option value="email">Email</option><option value="phone">Phone</option><option value="sms">SMS</option><option value="geo">Location</option><option value="vcard">Contact (vCard)</option></select></div>
  <div class="kind" data-k="text"><div class="field"><label for="v_text" id="v_label">Text or URL</label><textarea id="v_text" rows="3" placeholder="https://example.com"></textarea></div></div>
  <div class="kind" data-k="wifi" hidden><div class="gen-row"><div class="field"><label for="w_ssid">Network name</label><input id="w_ssid"></div><div class="field"><label for="w_pass">Password</label><input id="w_pass"></div><div class="field"><label for="w_sec">Security</label><select id="w_sec"><option>WPA</option><option>WEP</option><option value="nopass">None</option></select></div></div><label class="chk"><input type="checkbox" id="w_hid"> Hidden network</label></div>
  <div class="kind" data-k="email" hidden><div class="field"><label for="e_to">Address</label><input id="e_to" type="email"></div><div class="field"><label for="e_sub">Subject</label><input id="e_sub"></div><div class="field"><label for="e_body">Body</label><textarea id="e_body" rows="2"></textarea></div></div>
  <div class="kind" data-k="phone" hidden><div class="field"><label for="p_num">Number</label><input id="p_num" placeholder="+8801..."></div></div>
  <div class="kind" data-k="sms" hidden><div class="field"><label for="s_num">Number</label><input id="s_num"></div><div class="field"><label for="s_msg">Message</label><textarea id="s_msg" rows="2"></textarea></div></div>
  <div class="kind" data-k="geo" hidden><div class="gen-row"><div class="field"><label for="g_lat">Latitude</label><input id="g_lat" placeholder="23.8103"></div><div class="field"><label for="g_lng">Longitude</label><input id="g_lng" placeholder="90.4125"></div></div></div>
  <div class="kind" data-k="vcard" hidden><div class="gen-row"><div class="field"><label for="c_name">Full name</label><input id="c_name"></div><div class="field"><label for="c_org">Organization</label><input id="c_org"></div><div class="field"><label for="c_tel">Phone</label><input id="c_tel"></div><div class="field"><label for="c_mail">Email</label><input id="c_mail" type="email"></div><div class="field"><label for="c_url">Website</label><input id="c_url"></div></div></div>
  <input type="hidden" name="data" id="data"><input type="hidden" name="logo" id="logo">
 </section>
 <section class="card"><h2><?php echo L::icon('palette'); ?> Colours</h2>
  <div class="gen-row"><div class="field"><label for="fg">Foreground</label><input type="color" id="fg" name="fg" value="#000000"></div><div class="field"><label for="bg">Background</label><input type="color" id="bg" name="bg" value="#ffffff"></div><div class="field qr"><label for="eye">Eye colour</label><input type="color" id="eye" name="eye" value="#000000"></div><div class="field qr" id="g2" hidden><label for="fg2">Gradient end</label><input type="color" id="fg2" name="fg2" value="#0b3d91"></div></div>
  <label class="chk qr"><input type="checkbox" name="grad" value="1" id="grad"> Gradient foreground</label><label class="chk"><input type="checkbox" name="transparent" value="1"> Transparent background</label>
 </section>
 <section class="card qr"><h2><?php echo L::icon('sliders'); ?> QR style</h2>
  <div class="gen-row"><div class="field"><label for="shape">Modules</label><select id="shape" name="shape"><option value="square">Square</option><option value="rounded">Rounded</option><option value="dot">Dots</option></select></div><div class="field"><label for="eyeShape">Eyes</label><select id="eyeShape" name="eyeShape"><option value="square">Square</option><option value="rounded">Rounded</option><option value="circle">Circle</option></select></div><div class="field"><label for="ecc">Error correction</label><select id="ecc" name="ecc"><option value="L">Low (7%)</option><option value="M" selected>Medium (15%)</option><option value="Q">Quartile (25%)</option><option value="H">High (30%)</option></select></div></div>
  <div class="gen-row"><div class="field"><label for="frame">Frame</label><select id="frame" name="frame"><option value="">None</option><option value="square">Square</option><option value="rounded">Rounded</option></select></div><div class="field"><label for="caption">Caption</label><input id="caption" name="caption" maxlength="40" placeholder="Scan me"></div></div>
  <div class="field"><label for="logofile">Logo (centre image)</label><input type="file" id="logofile" accept="image/png,image/jpeg,image/webp,image/svg+xml"><button type="button" class="btn btn-secondary btn-sm" id="logoclr" hidden><?php echo L::icon('trash'); ?> Remove logo</button><p class="hint-line">Adding a logo switches error correction to High so the code stays scannable.</p></div>
 </section>
 <section class="card"><h2><?php echo L::icon('image'); ?> Size</h2>
  <div class="gen-row"><div class="field"><label for="size">Size (px)</label><input type="number" id="size" name="size" value="400" min="100" max="2000"></div><div class="field"><label for="margin">Margin</label><input type="number" id="margin" name="margin" value="4" min="0" max="20"></div></div>
  <label class="chk bar" hidden><input type="checkbox" name="text" value="1" checked> Show text under bars</label>
  <button type="button" class="btn btn-secondary" id="reset"><?php echo L::icon('rotate-ccw'); ?> Reset</button>
 </section>
</form>
<aside class="gen-side"><section class="card"><h2><?php echo L::icon('scan'); ?> Preview</h2>
 <div id="preview"><img id="img" alt="Generated code preview" hidden><p id="msg" class="hint-line">Start typing to see your code.</p></div>
 <div class="btn-row"><button type="button" class="btn btn-secondary" id="dsvg"><?php echo L::icon('download'); ?> SVG</button><button type="button" class="btn btn-secondary" id="dpng"><?php echo L::icon('image'); ?> PNG</button><button type="button" class="btn btn-secondary" id="cp"><?php echo L::icon('copy'); ?> <span>Copy PNG</span></button></div>
 <p class="hint-line"><?php echo L::icon('shield-check'); ?> Static codes: your content is encoded directly, never redirected. Nothing is stored.</p></section></aside>
</main>
<script src="/assets/js/app.js"></script>
<?php L::foot();
