<?php
header("content-type: image/png");

$w = $_GET["w"];
$d = $_GET["d"];
$l = $_GET["l"];

$tot = $w + $d + $l;

$wdeg = 360 * $w / $tot;
$ddeg = 360 * $d / $tot;
$ldeg = 360 - $wdeg - $ddeg;

$image = imagecreatetruecolor(106,106);

$bground = imagecolorallocate($image, 255, 255, 0xD3);
$red = imagecolorallocate($image, 255, 0, 0);
$green = imagecolorallocate($image, 0, 255, 0);
$blue = imagecolorallocate($image, 0, 0, 255);

imagefill($image, 0, 0, $bground);
if  ($wdeg > 0)
	imagefilledarc($image, 53, 53, 100, 100, 0, $wdeg, $green, IMG_ARC_PIE);
if  ($ddeg > 0)
	imagefilledarc($image, 53, 53, 100, 100, $wdeg, $wdeg+$ddeg, $blue, IMG_ARC_PIE);
if  ($ldeg > 0)
	imagefilledarc($image, 53, 53, 100, 100, $wdeg+$ddeg, 360, $red, IMG_ARC_PIE);
imagepng($image);
imagedestroy($image);
?>
