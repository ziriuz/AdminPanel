<?php
$code=(isset($_GET['code'])?$_GET['code']:'0');
$size=(isset($_GET['size'])?$_GET['size']:'90');
include('code128.class.php');
putenv('GDFONTPATH=' . realpath('.'));
$barcode = new phpCode128($code, $size, 'arial.ttf', 14);
$barcode->setEanStyle(false);
$barcode->setBorderWidth(0);
$barcode->setBorderSpacing(0);
$barcode->setPixelWidth(2);
$barcode->setShowText(false);
$barcode->getBarcode();