<?php
$carimage = "caricon.png";
$id = isset($_GET['id'])? $_GET['id']:0;
$im = imagecreatefrompng ($carimage); 
imagealphablending($im, true); // setting alpha blending on
imagesavealpha($im, true); // save alphablending setting (important)
// White background and blue text 
$bg = imagecolorallocate($im, 0, 0, 0);
$textcolor = imagecolorallocate($im, 255, 255, 255); 

// Write the string at the top left
$left = $id < 10 ? 10 : 5;
imagestring($im, 10, $left+1, 0+1, $id, $bg);
imagestring($im, 10, $left, 0, $id, $textcolor);


// Output the image
header('Content-type: image/png');

imagepng($im);
imagedestroy($im);