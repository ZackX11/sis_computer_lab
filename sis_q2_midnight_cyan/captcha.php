<?php
require_once 'config.php';
header('Content-Type: image/png');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
$chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
$code = '';
for ($i = 0; $i < 5; $i++) $code .= $chars[random_int(0, strlen($chars) - 1)];
$_SESSION['captcha'] = $code;
$im = imagecreatetruecolor(180, 58);
$bg = imagecolorallocate($im, 18, 35, 53);
$cyan = imagecolorallocate($im, 34, 211, 238);
$noise = imagecolorallocate($im, 70, 105, 130);
imagefill($im, 0, 0, $bg);
for ($i = 0; $i < 7; $i++) imageline($im, random_int(0,180), random_int(0,58), random_int(0,180), random_int(0,58), $noise);
for ($i = 0; $i < 45; $i++) imagesetpixel($im, random_int(0,179), random_int(0,57), $noise);
imagestring($im, 5, 48, 20, $code, $cyan);
imagepng($im);
imagedestroy($im);
