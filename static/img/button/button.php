<?php
if ($argc !== 2 || !preg_match('/.+\.png$/', $argv[1]))
    die("usage: php ${argv[0]} <*.png>\n");

button_img($argv[1]);
#pngquant -force 256 ../../public/img/button.png

function button_img($file)
{
    $img = new Imagick();
    $img->newImage(136, 224, 'transparent', 'png');
    $img->setImageDepth(8);

    $draw = new ImagickDraw();
    $draw->setTextAlignment(Imagick::ALIGN_CENTER);

    $draw->setFont('./msyhbd.ttf');
    $draw->setFontSize(16); //in pixel? doc say point.
    $draw->setFontWeight(700);

    $x = 0; $y = 0;
    draw_button($draw, $x, $y, 80, 33, 5, '#e33',    '去抢购', 24, 'v');
    draw_button($draw, $x, $y, 80, 33, 5, '#808080', '已结束', 24, 'v');
    draw_button($draw, $x, $y, 80, 33, 5, '#808080', '已抢光', 24, 'v');
    draw_button($draw, $x, $y, 80, 33, 5, '#393',     null,    24, 'hv');
    $y1 = $y;

    $y = 0;
    draw_hexagon($draw, $x, $y, 55, 15, 'l', '#cc0000', '频道',       'v');
    draw_hexagon($draw, $x, $y, 55, 15, 'r', '#cc0000', '收藏izheko', 'v');
    draw_hexagon($draw, $x, $y, 55, 15, 'l', '#cc0000', '客服',       'v');
    draw_hexagon($draw, $x, $y, 55, 15, 'r', '#888',    '回到顶部',   'v');

    $draw->setFont('./msyh.ttf');
    $draw->setFontSize(14);
    $draw->setFontWeight(500);

    /*
    $x = 0;
    draw_button2($draw, $x, $y, 60, 30, '#cc0000', '最热', 'h');
    draw_button3($draw, $x, $y, 60, 30, '#ccc',    '最新', 'v');
    $x = 0;
    draw_button2($draw, $x, $y, 60, 20, '#ccc',    '最热', 'h');
    draw_button3($draw, $x, $y, 60, 20, '#cc0000', '最新', 'v');
     */

    $draw->setFontSize(12);
    $x = 0;
    $y = $y1;
    draw_button($draw, $x, $y, 26, 17, 3, '#393',    '包邮',     13, 'v');
    draw_button($draw, $x, $y, 26, 17, 3, '#f39',    '拍改',     13, 'v');
    draw_button($draw, $x, $y, 34, 17, 3, '#ffa405', 'VIP价',    13, 'v');
    draw_button($draw, $x, $y, 40, 17, 3, '#e33',    '单品页',   13, 'v');

    draw_arrow($draw, $x, $y, 7, 14, '#999' );

    $img->drawImage($draw);
    $img->writeImage($file);
}

function draw_button($draw, &$x, &$y, $width, $height, $br, $bg, $text, $baseline, $flag = null)
{
    $draw->setFillColor($bg);
    $draw->roundRectangle($x, $y, $x + $width - 1, $y + $height - 1, $br, $br);
    if ($text) {
        $draw->setFillColor('#fff');
        $draw->annotation($x + ($width + 1) / 2, $y + $height / 2 + 0.4 * $draw->getFontSize(), $text);
    }
    move_position($x, $y, $width, $height, $flag);
}

function draw_hexagon($draw, &$x, &$y, $width, $w, $dr, $bg, $text, $flag = null)
{
    $draw->setFillColor($bg);
    if ($dr === 'l') $draw->polygon(array(
        array('x' => $x, 'y' => $y),
        array('x' => $x + $width - 1 - $w, 'y' => $y),
        array('x' => $x + $width - 1, 'y' => $y + $w),
        array('x' => $x + $width - 1, 'y' => $y + $width - 1),
        array('x' => $x + $w, 'y' => $y + $width - 1),
        array('x' => $x, 'y' => $y + $width - 1 - $w),
    ));
    else if ($dr === 'r') $draw->polygon(array(
        array('x' => $x + $w, 'y' => $y),
        array('x' => $x + $width - 1, 'y' => $y),
        array('x' => $x + $width - 1, 'y' => $y + $width - 1 - $w),
        array('x' => $x + $width - 1 - $w, 'y' => $y + $width - 1),
        array('x' => $x, 'y' => $y + $width - 1),
        array('x' => $x, 'y' => $y + $w),
    ));

    $draw->setFillColor('#fff');
    $mx = $x + ($width + 1) / 2;
    $my = $y + ($width + 1) / 2;
    mb_internal_encoding('UTF-8');
    if (mb_strlen($text) <= 2) {
        $draw->annotation($mx, $my + 6 , $text);
    } else if ($text === '收藏izheko') {
        $draw->annotation($mx, $my - 3 , mb_substr($text, 0, 2));
        $fs = $draw->getFontSize();
        $draw->setFontSize(12);
        $draw->annotation($mx, $my + 12, mb_substr($text, 2));
        $draw->setFontSize($fs);
    } else if ($text === '回到顶部') {
        $draw->annotation($mx, $my - 3 , mb_substr($text, 0, 2));
        $draw->annotation($mx, $my + 15, mb_substr($text, 2));
    }
    move_position($x, $y, $width, $width, $flag);
}

function draw_arrow($draw, &$x, &$y, $width, $height, $color, $flag = null)
{
    $draw->setStrokeWidth(1);
    $draw->setStrokeColor($color);
    #$draw->setFillColor('transparent');
    $draw->setFillColor('#f7f7f7');
    $draw->polyline(array(
        array('x' => $x, 'y' => $y),
        array('x' => $x + $width - 1, 'y' => $y + $height / 2),
        array('x' => $x, 'y' => $y + $height - 1),
    ));
    move_position($x, $y, $width, $height, $flag);
}

function draw_button2($draw, &$x, &$y, $width, $height, $bg, $text, $flag = null)
{
    $draw->setFillColor($bg);
    $draw->arc($x, $y, $x + $height - 1, $y + $height - 1, 90, 270);
    $draw->rectangle($x + $height / 2, $y, $x + $width - 1, $y + $height - 1);
    if ($text) {
        $draw->setFillColor('#fff');
        $draw->annotation($x + $height / 8 + ($width + 1) / 2, $y + $height / 2 + 0.4 * $draw->getFontSize(), $text);
    }
    move_position($x, $y, $width, $height, $flag);
}

function draw_button3($draw, &$x, &$y, $width, $height, $bg, $text, $flag = null)
{
    $draw->setFillColor($bg);
    $draw->arc($x + $width - $height, $y, $x + $width - 1, $y + $height - 1, 270, 90);
    $draw->rectangle($x, $y, $x + $width - $height / 2 - 1, $y + $height - 1);
    if ($text) {
        $draw->setFillColor('#fff');
        $draw->annotation($x - $height / 8 + ($width + 1) / 2, $y + $height / 2 + 0.4 * $draw->getFontSize(), $text);
    }
    move_position($x, $y, $width, $height, $flag);
}

function move_position(&$x, &$y, $width, $height, $flag = null)
{
    if ($flag === null) return;
    else if ($flag === 'h')    $x += $width  + 1;
    else if ($flag === 'v')    $y += $height + 1;
    else if ($flag === 'hv') { $x += $width  + 1; $y += $height + 1; }
}

