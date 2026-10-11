<?php
declare(strict_types=1);
require __DIR__.'/../src/Qr.php';require __DIR__.'/../src/Barcode.php';require __DIR__.'/../src/Render.php';
use App\{Qr,Barcode,Render};
header('Cache-Control: no-store');header('X-Content-Type-Options: nosniff');
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('POST only');}
$data=(string)($_POST['data']??'');$type=(string)($_POST['type']??'qr');
try{
 if(strlen($data)>2000)throw new InvalidArgumentException('Content is too long (max 2000 characters).');
 $svg=$type==='qr'?Render::qr(Qr::encode($data,(string)($_POST['ecc']??'M')),$_POST):Render::bar(Barcode::encode($type,$data),$_POST);
 header('Content-Type: image/svg+xml');echo $svg;
}catch(Throwable $e){http_response_code(400);header('Content-Type: text/plain; charset=utf-8');echo $e instanceof InvalidArgumentException||$e instanceof RuntimeException?$e->getMessage():'Could not generate this code.';}
