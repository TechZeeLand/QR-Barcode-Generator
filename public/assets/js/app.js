(function () {
    'use strict';
    var $ = function (s) { return document.querySelector(s); };
    var $$ = function (s) { return Array.prototype.slice.call(document.querySelectorAll(s)); };
    var main = $('.gen'), f = $('#f');
    if (!main || !f) { return; }
    var cfg = {};
    try { cfg = JSON.parse(main.getAttribute('data-cfg') || '{}'); } catch (e) {}
    var blob = null, svgText = '', timer, seq = 0;

    var v = function (id) { return $(id).value.trim(); };
    var esc = function (s) { return s.replace(/([\\;,:"])/g, '\\$1'); };
    var vc = function (s) { return s.replace(/([\\;,])/g, '\\$1').replace(/\n/g, '\\n'); };
    var ics = function (s) { return s ? s.replace(/[-:]/g, '').replace(/\.\d+/, '') + '00' : ''; };
    var HINT = { code128: 'Text (an even count of digits uses the compact set C)', code39: 'A-Z 0-9 - . $ / + % space', ean13: '12 digits (check digit added)', ean8: '7 digits (check digit added)', upca: '11 digits (check digit added)', itf: 'Digits (an even count)', itf14: '13 digits (check digit added)', codabar: '0-9 - $ : / . +', msi: 'Digits', pharmacode: 'A number from 3 to 131070' };

    function payload() {
        var ty = $('#type').value;
        if (ty !== 'qr') { return v('#v_text'); }
        switch ($('#kind').value) {
            case 'wifi': return v('#w_ssid') ? 'WIFI:T:' + $('#w_sec').value + ';S:' + esc(v('#w_ssid')) + ';P:' + esc(v('#w_pass')) + ';H:' + $('#w_hid').checked + ';;' : '';
            case 'email': return v('#e_to') ? 'mailto:' + v('#e_to') + '?subject=' + encodeURIComponent(v('#e_sub')) + '&body=' + encodeURIComponent(v('#e_body')) : '';
            case 'phone': return v('#p_num') ? 'tel:' + v('#p_num') : '';
            case 'sms': return v('#s_num') ? 'SMSTO:' + v('#s_num') + ':' + v('#s_msg') : '';
            case 'whatsapp': return v('#wa_num') ? 'https://wa.me/' + v('#wa_num').replace(/\D/g, '') + (v('#wa_msg') ? '?text=' + encodeURIComponent(v('#wa_msg')) : '') : '';
            case 'geo': return v('#g_lat') && v('#g_lng') ? 'geo:' + v('#g_lat') + ',' + v('#g_lng') : '';
            case 'event': return v('#ev_title') && v('#ev_start') ? ['BEGIN:VEVENT', 'SUMMARY:' + vc(v('#ev_title')), 'DTSTART:' + ics($('#ev_start').value), v('#ev_end') && 'DTEND:' + ics($('#ev_end').value), v('#ev_loc') && 'LOCATION:' + vc(v('#ev_loc')), 'END:VEVENT'].filter(Boolean).join('\n') : '';
            case 'vcard': return v('#c_name') ? ['BEGIN:VCARD', 'VERSION:3.0', 'N:;' + vc(v('#c_name')) + ';;;', 'FN:' + vc(v('#c_name')), v('#c_org') && 'ORG:' + vc(v('#c_org')), v('#c_tel') && 'TEL:' + v('#c_tel'), v('#c_mail') && 'EMAIL:' + v('#c_mail'), v('#c_url') && 'URL:' + v('#c_url'), 'END:VCARD'].filter(Boolean).join('\n') : '';
            default: return v('#v_text');
        }
    }

    /* ---------- visibility depending on type ---------- */
    function ui() {
        var q = $('#type').value === 'qr';
        $('#qrOnly').hidden = !q;
        $$('.qr').forEach(function (e) { e.hidden = !q; });
        $$('.bar').forEach(function (e) { e.hidden = q; });
        $('#g2').hidden = !q || !$('#grad').checked;
        var k = q ? $('#kind').value : 'text';
        $$('.kind').forEach(function (e) { e.hidden = e.getAttribute('data-k') !== k; });
        $('#v_label').textContent = q ? 'Text or URL' : 'Content';
        $('#v_text').placeholder = q ? 'https://example.com' : (HINT[$('#type').value] || '');
        $('#v_hint').textContent = q ? '' : (HINT[$('#type').value] || '');
        $('#margin_label').textContent = q ? 'Margin (modules)' : 'Quiet zone (modules)';
        if (!q && +$('#margin').value < 10 && !ui.touchedMargin) { $('#margin').value = 10; }
        if (q && +$('#margin').value === 10 && !ui.touchedMargin) { $('#margin').value = cfg.margin || 4; }
    }
    $('#margin').addEventListener('input', function () { ui.touchedMargin = true; });

    /* ---------- contrast hint ---------- */
    function lum(hex) {
        var c = [1, 3, 5].map(function (i) { var x = parseInt(hex.substr(i, 2), 16) / 255; return x <= 0.03928 ? x / 12.92 : Math.pow((x + 0.055) / 1.055, 2.4); });
        return 0.2126 * c[0] + 0.7152 * c[1] + 0.0722 * c[2];
    }
    function contrast() {
        var el = $('#contrast'), a = lum($('#fg').value), b = lum($('#bg').value);
        var ratio = (Math.max(a, b) + 0.05) / (Math.min(a, b) + 0.05), msg = '', cls = '';
        if ($('#transparent').checked) { msg = 'Transparent background: make sure the code is placed on a light, plain area.'; cls = 'warn'; }
        else if (ratio < 3) { msg = 'Low contrast (' + ratio.toFixed(1) + ':1). Many scanners will struggle. Aim for 4:1 or more.'; cls = 'warn'; }
        else if (a > b) { msg = 'Light-on-dark (inverted) codes are not read by some scanners. Test before printing.'; cls = 'warn'; }
        else { msg = 'Contrast ' + ratio.toFixed(1) + ':1. Good for scanning.'; cls = 'ok'; }
        el.textContent = msg; el.className = 'contrast ' + cls;
    }

    /* ---------- state (presets + history) ---------- */
    var STYLE_IDS = ['fg', 'bg', 'eye', 'fg2', 'grad', 'transparent', 'shape', 'eyeShape', 'ecc', 'frame', 'caption', 'size', 'margin'];
    function readState(ids) {
        var s = {};
        ids.forEach(function (id) { var el = document.getElementById(id); if (el) { s[id] = el.type === 'checkbox' ? el.checked : el.value; } });
        return s;
    }
    function writeState(s) {
        Object.keys(s).forEach(function (id) { var el = document.getElementById(id); if (!el || el.type === 'file') { return; } if (el.type === 'checkbox') { el.checked = !!s[id]; } else { el.value = s[id]; } });
    }
    function allIds() { return $$('#f [id]').filter(function (e) { return e.id !== 'logo' && e.id !== 'logofile' && e.id !== 'data' && e.id !== 'preset-name' && e.id !== 'lines'; }).map(function (e) { return e.id; }); }
    function store(key, val) { try { localStorage.setItem(key, JSON.stringify(val)); } catch (e) {} }
    function load(key) { try { return JSON.parse(localStorage.getItem(key) || '[]'); } catch (e) { return []; } }

    function renderPresets() {
        var box = $('#presets'), list = load('tzl-qr-presets');
        box.innerHTML = '';
        list.forEach(function (p, i) {
            var b = document.createElement('span'); b.className = 'chip-wrap';
            var use = document.createElement('button'); use.type = 'button'; use.className = 'chip'; use.textContent = p.name;
            use.addEventListener('click', function () { writeState(p.s); go(true); });
            var del = document.createElement('button'); del.type = 'button'; del.className = 'chip-x'; del.setAttribute('aria-label', 'Delete preset ' + p.name); del.textContent = '×';
            del.addEventListener('click', function () { var l = load('tzl-qr-presets'); l.splice(i, 1); store('tzl-qr-presets', l); renderPresets(); });
            b.appendChild(use); b.appendChild(del); box.appendChild(b);
        });
    }
    $('#preset-save').addEventListener('click', function () {
        var n = $('#preset-name').value.trim() || ('Style ' + (load('tzl-qr-presets').length + 1));
        var l = load('tzl-qr-presets').filter(function (p) { return p.name !== n; });
        l.unshift({ name: n, s: readState(STYLE_IDS) });
        store('tzl-qr-presets', l.slice(0, 12)); $('#preset-name').value = ''; renderPresets();
    });

    function renderHistory() {
        var box = $('#history'), list = load('tzl-qr-history');
        box.innerHTML = '';
        $('#history-empty').hidden = list.length > 0;
        list.forEach(function (h) {
            var b = document.createElement('button'); b.type = 'button'; b.className = 'chip'; b.textContent = h.label;
            b.addEventListener('click', function () { writeState(h.s); go(true); });
            box.appendChild(b);
        });
    }
    function remember() {
        var d = $('#data').value; if (!d) { return; }
        var label = ($('#type').value === 'qr' ? 'QR' : $('#type').value) + ': ' + d.replace(/\s+/g, ' ').slice(0, 24);
        var l = load('tzl-qr-history').filter(function (h) { return h.label !== label; });
        l.unshift({ label: label, s: readState(allIds()) });
        store('tzl-qr-history', l.slice(0, 8)); renderHistory();
    }

    /* ---------- render ---------- */
    function apiUrl() {
        var ty = $('#type').value, p = new URLSearchParams();
        p.set('data', $('#data').value);
        if (ty !== 'qr') { p.set('type', ty); }
        ['fg', 'bg', 'size', 'margin'].forEach(function (id) { p.set(id, $('#' + id).value); });
        if (ty === 'qr') {
            ['eye', 'shape', 'eyeShape', 'ecc', 'frame', 'caption'].forEach(function (id) { if ($('#' + id).value) { p.set(id, $('#' + id).value); } });
            if ($('#grad').checked) { p.set('grad', '1'); p.set('fg2', $('#fg2').value); }
        }
        if ($('#transparent').checked) { p.set('transparent', '1'); }
        return location.origin + (ty === 'qr' ? '/api/qr.svg?' : '/api/barcode.svg?') + p.toString();
    }

    function render() {
        ui(); contrast();
        var d = payload(); $('#data').value = d;
        var img = $('#img'), m = $('#msg'), my = ++seq;
        if (!d) { img.hidden = true; m.hidden = false; m.textContent = 'Start typing to see your code.'; blob = null; svgText = ''; $('#apiurl').textContent = '…'; return; }
        fetch('/api/generate', { method: 'POST', body: new FormData(f) }).then(function (r) {
            if (my !== seq) { return null; }
            if (!r.ok) { return r.text().then(function (t) { throw new Error(t); }); }
            return r.blob();
        }).then(function (b) {
            if (!b || my !== seq) { return; }
            blob = b; b.text().then(function (t) { svgText = t; });
            var old = img.src; img.src = URL.createObjectURL(b); if (old.indexOf('blob:') === 0) { URL.revokeObjectURL(old); }
            img.hidden = false; m.hidden = true;
            var u = apiUrl(); $('#apiurl').textContent = u.length > 1500 ? 'Content is too long for a URL. Use the POST endpoint (see docs).' : u;
        }).catch(function (e) {
            if (my !== seq) { return; }
            blob = null; svgText = ''; img.hidden = true; m.hidden = false; m.textContent = e.message || 'Could not generate this code.';
        });
    }
    function go(now) { clearTimeout(timer); timer = setTimeout(render, now ? 0 : 250); }
    f.addEventListener('input', function () { go(); });
    f.addEventListener('change', function () { go(); });

    /* ---------- logo ---------- */
    var lf = $('#logofile');
    if (lf) {
        lf.addEventListener('change', function (e) {
            var fl = e.target.files[0]; if (!fl) { return; }
            if (fl.size > 300000) { alert('Logo is too large (max 300 KB).'); e.target.value = ''; return; }
            var r = new FileReader(); r.onload = function () { $('#logo').value = r.result; $('#logoclr').hidden = false; go(true); }; r.readAsDataURL(fl);
        });
        $('#logoclr').addEventListener('click', function () { $('#logo').value = ''; lf.value = ''; $('#logoclr').hidden = true; go(true); });
    }

    /* ---------- downloads ---------- */
    function counted() { var fd = new FormData(f); fd.append('count', '1'); fetch('/api/generate', { method: 'POST', body: fd }).catch(function () {}); remember(); }
    function save(b, n) { var a = document.createElement('a'); a.href = URL.createObjectURL(b); a.download = n; a.click(); setTimeout(function () { URL.revokeObjectURL(a.href); }, 1000); }
    function png() {
        return new Promise(function (ok, no) {
            if (!blob) { return no(); }
            var i = new Image();
            i.onload = function () { var c = document.createElement('canvas'); c.width = i.naturalWidth || +$('#size').value; c.height = i.naturalHeight || c.width; c.getContext('2d').drawImage(i, 0, 0, c.width, c.height); c.toBlob(function (b) { b ? ok(b) : no(); }, 'image/png'); };
            i.onerror = no; i.src = $('#img').src;
        });
    }
    function fileName(ext) { var t = $('#type').value; return (t === 'qr' ? 'qr-code' : t) + '.' + ext; }
    function flash(btn, txt) { var s = btn.querySelector('span') || btn, o = s.textContent; s.textContent = txt; setTimeout(function () { s.textContent = o; }, 1500); }
    $('#dsvg').addEventListener('click', function () { if (blob) { save(blob, fileName('svg')); counted(); } });
    $('#dpng').addEventListener('click', function () { png().then(function (b) { save(b, fileName('png')); counted(); }).catch(function () {}); });
    $('#cp').addEventListener('click', function () {
        var btn = this;
        png().then(function (b) { return navigator.clipboard.write([new ClipboardItem({ 'image/png': b })]); }).then(function () { flash(btn, 'Copied'); counted(); }).catch(function () { flash(btn, 'Not supported'); });
    });
    $('#cpsvg').addEventListener('click', function () {
        var btn = this; if (!svgText) { return; }
        window.TZL.copy(svgText).then(function () { flash(btn, 'Copied'); counted(); }).catch(function () { flash(btn, 'Failed'); });
    });

    /* ---------- reset / batch ---------- */
    $('#reset').addEventListener('click', function () {
        f.reset(); $('#logo').value = ''; if ($('#logoclr')) { $('#logoclr').hidden = true; }
        $$('#f textarea, #f input:not([type=color]):not([type=checkbox]):not([type=number]):not([type=hidden]):not([type=file])').forEach(function (e) { e.value = ''; });
        ui.touchedMargin = false; go(true);
    });
    var bb = $('#batch');
    if (bb) {
        bb.addEventListener('click', function () {
            var msg = $('#batch-msg'); msg.textContent = 'Working…';
            var fd = new FormData(f); fd.delete('data'); fd.append('lines', $('#lines').value);
            fetch('/api/batch', { method: 'POST', body: fd }).then(function (r) {
                if (!r.ok) { return r.text().then(function (t) { throw new Error(t); }); }
                return r.blob();
            }).then(function (b) { save(b, 'codes.zip'); msg.textContent = 'Done.'; }).catch(function (e) { msg.textContent = e.message || 'Failed.'; });
        });
    }

    renderPresets(); renderHistory(); render();
})();
