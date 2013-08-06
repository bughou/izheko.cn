<?php
$img = new Imagick();
$img->newImage(128, 60, 'transparent', 'png');

$draw = new ImagickDraw();
$draw->setFont('./pangwa.ttf');
$draw->setTextAlignment(2);
$draw->setFillColor('#77a610');

$draw->setFontSize(40);
$img->annotateImage($draw, 64, 35, 0, '爱折扣');

$draw->setFontSize(18);
$draw->setTextKerning(Imagick::ALIGN_CENTER);
$img->annotateImage($draw, 64, 57, 0, 'izheko.cn');

$img->writeImage($argv[1]);

function logo_by_gd()
{
    $img = imagecreate(128, 60);
    imagecolorallocate($img, 0xff, 0xff, 0xff);

    $c = imagecolorallocate($img, 0xff, 0, 0);
    imagettftext($img, 40 * 0.75, 0, 0, 40, $c, './pangwa.ttf', '爱折扣');

    imagepng($img, $_SERVER['argv'][1]);
}