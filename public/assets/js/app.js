(()=>{const $=s=>document.querySelector(s),f=$('#f');let blob=null,t;
const kinds=document.querySelectorAll('.kind'),v=id=>$(id).value.trim(),esc=s=>s.replace(/([\\;,:"])/g,'\\$1');
function payload(){if($('#type').value!=='qr')return v('#v_text');
 switch($('#kind').value){
 case'wifi':return`WIFI:T:${$('#w_sec').value};S:${esc(v('#w_ssid'))};P:${esc(v('#w_pass'))};;`;
 case'email':return v('#e_to')?`mailto:${v('#e_to')}?subject=${encodeURIComponent(v('#e_sub'))}&body=${encodeURIComponent(v('#e_body'))}`:'';
 case'phone':return v('#p_num')?'tel:'+v('#p_num'):'';
 case'sms':return v('#s_num')?`SMSTO:${v('#s_num')}:${v('#s_msg')}`:'';
 case'geo':return v('#g_lat')&&v('#g_lng')?`geo:${v('#g_lat')},${v('#g_lng')}`:'';
 default:return v('#v_text');}}
function ui(){const q=$('#type').value==='qr';$('#qrOnly').hidden=!q;document.querySelectorAll('.qr').forEach(e=>e.hidden=!q);document.querySelectorAll('.bar').forEach(e=>e.hidden=q);
 const k=q?$('#kind').value:'text';kinds.forEach(e=>e.hidden=e.dataset.k!==k);}
async function render(){ui();const d=payload();$('#data').value=d;const img=$('#img'),m=$('#msg');
 if(!d){img.hidden=true;m.hidden=false;m.textContent='Start typing to see your code.';blob=null;return;}
 try{const r=await fetch('api.php',{method:'POST',body:new FormData(f)});
  if(!r.ok){throw new Error(await r.text());}
  blob=await r.blob();img.src=URL.createObjectURL(blob);img.hidden=false;m.hidden=true;
 }catch(e){blob=null;img.hidden=true;m.hidden=false;m.textContent=e.message||'Could not generate this code.';}}
const go=()=>{clearTimeout(t);t=setTimeout(render,250)};
document.addEventListener('input',go);document.addEventListener('change',go);
const save=(b,n)=>{const a=document.createElement('a');a.href=URL.createObjectURL(b);a.download=n;a.click();setTimeout(()=>URL.revokeObjectURL(a.href),1e3)};
const png=()=>new Promise((ok,no)=>{if(!blob)return no();const i=new Image();i.onload=()=>{const c=document.createElement('canvas');c.width=i.naturalWidth||+f.size.value;c.height=i.naturalHeight||c.width;c.getContext('2d').drawImage(i,0,0,c.width,c.height);c.toBlob(ok,'image/png')};i.onerror=no;i.src=$('#img').src});
$('#dsvg').onclick=()=>blob&&save(blob,'code.svg');
$('#dpng').onclick=async()=>{try{save(await png(),'code.png')}catch{}};
$('#cp').onclick=async()=>{try{await navigator.clipboard.write([new ClipboardItem({'image/png':await png()})]);$('#cp').lastChild.textContent=' Copied'}catch{$('#cp').lastChild.textContent=' Not supported'}setTimeout(()=>$('#cp').lastChild.textContent=' Copy PNG',1500)};
render();})();
