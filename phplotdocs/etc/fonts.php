<?php
# Font comparison - GD and TTF font in various sizes

# TrueType font directory:
$ttf_dir = '/usr/local/lib/fonts/truetype';

# Make an image:
$width = 410;
$height = 150;
$g = imagecreate($width, $height);

# Allocate colors:
$bgnd = imagecolorallocate($g, 255, 255, 255); # Background
$fgnd = imagecolorallocate($g, 0, 0, 0); # Text

# Border around the whole thing:
imagerectangle($g, 0, 0, $width-1, $height-1, $fgnd);

# Built-in Fonts:
$x = 10;
$y = 20;
for ($font = 1; $font <= 5; $font++) {
  imagestring($g, "$font", $x, $y, "Built-in GD Font $font", $fgnd);
  $y += imagefontheight($font) + 5;
}

# TrueType Font:
$x = 180;
$y = 20;
for ($size = 8.0; $size <= 18.0; $size += 2.0) {
  $bb = imagettftext($g, $size, 0.0, $x, $y, $fgnd, "$ttf_dir/arial.ttf",
    "TrueType Arial {$size}pt");
  $y += $bb[1] - $bb[7] + 6;
}

# Output the image:
imagepng($g);
