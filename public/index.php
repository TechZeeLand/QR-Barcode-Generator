<?php
declare(strict_types=1);
require __DIR__.'/../src/Layout.php';require __DIR__.'/../src/Barcode.php';
use App\Layout;
Layout::head('QR Code & Barcode Generator — free, private, open source');
?>
<main class="app">
<form id="f" class="panel" autocomplete="off">
 <h1>Create a code</h1>
 <label>Type<select name="type" id="type"><option value="qr">QR code</option><?php foreach(App\Barcode::TYPES as $k=>$v)echo "<option value=\"$k\">$v</option>"; ?></select></label>
 <div id="qrOnly">
  <label>Content<select id="kind"><option value="text">Text / URL</option><option value="wifi">Wi-Fi</option><option value="email">Email</option><option value="phone">Phone</option><option value="sms">SMS</option><option value="geo">Location</option></select></label>
 </div>
 <div class="kind" data-k="text"><label>Text or URL<textarea id="v_text" rows="3" placeholder="https://example.com"></textarea></label></div>
 <div class="kind" data-k="wifi" hidden><label>Network name<input id="w_ssid"></label><label>Password<input id="w_pass"></label><label>Security<select id="w_sec"><option>WPA</option><option>WEP</option><option value="nopass">None</option></select></label></div>
 <div class="kind" data-k="email" hidden><label>Address<input id="e_to" type="email"></label><label>Subject<input id="e_sub"></label><label>Body<textarea id="e_body" rows="2"></textarea></label></div>
 <div class="kind" data-k="phone" hidden><label>Number<input id="p_num" placeholder="+8801…"></label></div>
 <div class="kind" data-k="sms" hidden><label>Number<input id="s_num"></label><label>Message<textarea id="s_msg" rows="2"></textarea></label></div>
 <div class="kind" data-k="geo" hidden><label>Latitude<input id="g_lat"></label><label>Longitude<input id="g_lng"></label></div>
 <input type="hidden" name="data" id="data">
 <fieldset><legend>Style</legend>
  <div class="row"><label>Colour<input type="color" name="fg" value="#000000"></label><label>Background<input type="color" name="bg" value="#ffffff"></label><label class="qr">Eye colour<input type="color" name="eye" value="#000000"></label></div>
  <div class="row"><label class="qr">Modules<select name="shape"><option value="square">Square</option><option value="rounded">Rounded</option><option value="dot">Dots</option></select></label>
  <label class="qr">Eyes<select name="eyeShape"><option value="square">Square</option><option value="rounded">Rounded</option><option value="circle">Circle</option></select></label>
  <label class="qr">Error correction<select name="ecc"><option value="L">Low</option><option value="M" selected>Medium</option><option value="Q">Quartile</option><option value="H">High</option></select></label></div>
  <div class="row"><label>Size (px)<input type="number" name="size" value="400" min="100" max="2000"></label><label>Margin<input type="number" name="margin" value="4" min="0" max="10"></label></div>
  <label class="chk"><input type="checkbox" name="transparent" value="1"> Transparent background</label>
  <label class="chk bar"><input type="checkbox" name="text" value="1" checked> Show text under bars</label>
 </fieldset>
</form>
<section class="panel out">
 <div id="preview" aria-live="polite"><img id="img" alt="Generated code preview" hidden><p id="msg" class="muted">Start typing to see your code.</p></div>
 <div class="btns"><button type="button" id="dsvg"><?php echo Layout::icon('download'); ?> SVG</button><button type="button" id="dpng"><?php echo Layout::icon('image'); ?> PNG</button><button type="button" id="cp"><?php echo Layout::icon('copy'); ?> Copy PNG</button></div>
 <p class="muted small"><?php echo Layout::icon('shield-check'); ?> Your content is rendered on this server for the preview only and is never stored or logged by the app.</p>
</section>
</main>
<script src="/assets/js/app.js"></script>
<?php Layout::foot();
