(()=>{const $=s=>document.querySelector(s),$$=s=>document.querySelectorAll(s),f=$('#f');let blob=null,t,seq=0;
const v=id=>$(id).value.trim(),esc=s=>s.replace(/([\\;,:"])/g,'\\$1'),vc=s=>s.replace(/([\\;,])/g,'\\$1').replace(/\n/g,'\\n');
const HINT={code128:'Text (digits-only even length uses compact set C)',code39:'A-Z 0-9 - . $ / + % space',ean13:'12 digits',ean8:'7 digits',upca:'11 digits',itf:'Digits (even count)',itf14:'13 digits',codabar:'0-9 - $ : / . +',msi:'Digits',pharmacode:'Number 3 to 131070'};
function payload(){const ty=$('#type').value;if(ty!=='qr')return v('#v_text');
 switch($('#kind').value){
 case'wifi':return v('#w_ssid')?`WIFI:T:${$('#w_sec').value};S:${esc(v('#w_ssid'))};P:${esc(v('#w_pass'))};H:${$('#w_hid').checked};;`:'';
 case'email':return v('#e_to')?`mailto:${v('#e_to')}?subject=${encodeURIComponent(v('#e_sub'))}&body=${encodeURIComponent(v('#e_body'))}`:'';
 case'phone':return v('#p_num')?'tel:'+v('#p_num'):'';
 case'sms':return v('#s_num')?`SMSTO:${v('#s_num')}:${v('#s_msg')}`:'';
 case'geo':return v('#g_lat')&&v('#g_lng')?`geo:${v('#g_lat')},${v('#g_lng')}`:'';
 case'vcard':return v('#c_name')?['BEGIN:VCARD','VERSION:3.0','N:;'+vc(v('#c_name'))+';;;','FN:'+vc(v('#c_name')),v('#c_org')&&'ORG:'+vc(v('#c_org')),v('#c_tel')&&'TEL:'+v('#c_tel'),v('#c_mail')&&'EMAIL:'+v('#c_mail'),v('#c_url')&&'URL:'+v('#c_url'),'END:VCARD'].filter(Boolean).join('\n'):'';
 default:return v('#v_text');}}
function ui(){const q=$('#type').value==='qr';$('#qrOnly').hidden=!q;$$('.qr').forEach(e=>e.hidden=!q);$$('.bar').forEach(e=>e.hidden=q);
 $('#g2').hidden=!q||!$('#grad').checked;const k=q?$('#kind').value:'text';$$('.kind').forEach(e=>e.hidden=e.dataset.k!==k);
 $('#v_label').textContent=q?'Text or URL':'Content';$('#v_text').placeholder=q?'https://example.com':(HINT[$('#type').value]||'');}
async function render(){ui();const d=payload();$('#data').value=d;const img=$('#img'),m=$('#msg'),my=++seq;
 if(!d){img.hidden=true;m.hidden=false;m.textContent='Start typing to see your code.';blob=null;return;}
 try{const r=await fetch('api.php',{method:'POST',body:new FormData(f)});if(my!==seq)return;
  if(!r.ok)throw new Error(await r.text());
  blob=await r.blob();const old=img.src;img.src=URL.createObjectURL(blob);if(old.startsWith('blob:'))URL.revokeObjectURL(old);img.hidden=false;m.hidden=true;
 }catch(e){if(my!==seq)return;blob=null;img.hidden=true;m.hidden=false;m.textContent=e.message||'Could not generate this code.';}}
const go=()=>{clearTimeout(t);t=setTimeout(render,250)};
f.addEventListener('input',go);f.addEventListener('change',go);
$('#logofile').addEventListener('change',e=>{const fl=e.target.files[0];if(!fl)return;
 if(fl.size>300000){alert('Logo is too large (max 300 KB).');e.target.value='';return;}
 const r=new FileReader();r.onload=()=>{$('#logo').value=r.result;$('#logoclr').hidden=false;go()};r.readAsDataURL(fl)});
$('#logoclr').onclick=()=>{$('#logo').value='';$('#logofile').value='';$('#logoclr').hidden=true;go()};
$('#reset').onclick=()=>{f.reset();$('#logo').value='';$('#logoclr').hidden=true;$$('textarea,input:not([type=color]):not([type=checkbox]):not([type=number]):not([type=hidden])').forEach(e=>e.value='');render()};
const save=(b,n)=>{const a=document.createElement('a');a.href=URL.createObjectURL(b);a.download=n;a.click();setTimeout(()=>URL.revokeObjectURL(a.href),1e3)};
const png=()=>new Promise((ok,no)=>{if(!blob)return no();const i=new Image();i.onload=()=>{const c=document.createElement('canvas');c.width=i.naturalWidth||+$('#size').value;c.height=i.naturalHeight||c.width;c.getContext('2d').drawImage(i,0,0,c.width,c.height);c.toBlob(b=>b?ok(b):no(),'image/png')};i.onerror=no;i.src=$('#img').src});
$('#dsvg').onclick=()=>blob&&save(blob,'code.svg');
$('#dpng').onclick=async()=>{try{save(await png(),'code.png')}catch{}};
$('#cp').onclick=async()=>{const s=$('#cp span');try{await navigator.clipboard.write([new ClipboardItem({'image/png':await png()})]);s.textContent='Copied'}catch{s.textContent='Not supported'}setTimeout(()=>s.textContent='Copy PNG',1500)};
render();})();
