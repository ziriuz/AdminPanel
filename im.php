<?php
$IMAGES_FOLDER = '../../../../mycomanda.com/www/prd_lib/images';
if(isset($_GET['nm'])) $nm=$_GET['nm'];
else die();
$file=$IMAGES_FOLDER.'/'.$nm;
$str='mycomanda.com';
// Set the enviroment variable for GD
putenv('GDFONTPATH=' . realpath('./prd_images'));
if (!file_exists($file))die();
if(!$img= ImageCreateFromJPEG($file))die();
$size_x = 253;
$size_y = 353;
// ширина одного символа
$space_per_char = 0.5*$size_x/(strlen($str));
if (file_exists('prd_images/arial.ttf'))
{
 $color = imagecolorallocate($img,51,40,46);
 imagettftext($img, 10, 0, 0.3*$size_x+$space_per_char, $size_y-46, $color, 'arial', $str);
}
header('Content-type: image/jpg');
imagejpeg($img,null,96);
?>
