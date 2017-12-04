<?php
#===========================================================================
#= Script : phpCode128
#= File   : example.php
#= Version: 0.1
#= Author : Mike Leigh
#= Email  : mike@mikeleigh.com
#= Website: http://www.mikeleigh.com/scripts/phpcode128/
#= Support: http://www.mikeleigh.com/forum
#===========================================================================
#= Copyright (c) 2006 Mike Leigh
#= You are free to use and modify this script as long as this header
#= section stays intact
#=
#= This file is part of phpCode128.
#=
#= phpFile is free software; you can redistribute it and/or modify
#= it under the terms of the GNU General Public License as published by
#= the Free Software Foundation; either version 2 of the License, or
#= (at your option) any later version.
#=
#= phpFile is distributed in the hope that it will be useful,
#= but WITHOUT ANY WARRANTY; without even the implied warranty of
#= MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#= GNU General Public License for more details.
#=
#= You should have received a copy of the GNU General Public License
#= along with DownloadCounter; if not, write to the Free Software
#= Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#===========================================================================
include('code128.class.php');
putenv('GDFONTPATH=' . realpath('.'));

echo "<h2>Examples of using the code 128 php class</h2>";
echo "<p>All the examples here will use the string mikeleigh.com and the examples will showcase the different styles of barcode that can be produced witht he options</p>";

$barcode = new phpCode128('mikeleigh.com', 150, 'arial.ttf', 18);
$barcode->saveBarcode('1.png');
echo "<h3>Using the default class</h3>";
echo "<p>Font is set to verdanna with size 18 and the image size to 150.  The following defautls are used.  These are the same as the class defaults</p>";
echo '<pre>
	$barcode->setBorderWidth(2);
	$barcode->setBorderSpacing(10);
	$barcode->setPixelWidth(1);
	$barcode->setEanStyle(true);
	$barcode->setShowText(true);
	$barcode->setAutoAdjustFontSize(true);
	$barcode->setTextSpacing(5);
</pre>';
echo "<img src='1.png'>";
echo "<hr />";

$barcode = new phpCode128('mikeleigh.com', 150, 'arial.ttf', 18);
$barcode->setBorderWidth(0);
$barcode->setBorderSpacing(0);
$barcode->saveBarcode('2.png');
echo "<h3>Removing the border</h3>";
echo "<p>setBorderWidth and setBorderSpacing set to 0</p>";
echo '<pre>
	$barcode->setBorderWidth(0);
	$barcode->setBorderSpacing(0);
	$barcode->setPixelWidth(1);
	$barcode->setEanStyle(true);
	$barcode->setShowText(true);
	$barcode->setAutoAdjustFontSize(true);
	$barcode->setTextSpacing(5);
</pre>';
echo "<img src='2.png'>";
echo "<hr />";

$barcode = new phpCode128('mikeleigh.com', 150, 'arial.ttf', 18);
$barcode->setPixelWidth(2);
$barcode->saveBarcode('3.png');
echo "<h3>Increasing the pixel width</h3>";
echo "<p>setPixelWidth set to 2</p>";
echo '<pre>
	$barcode->setBorderWidth(2);
	$barcode->setBorderSpacing(10);
	$barcode->setPixelWidth(2);
	$barcode->setEanStyle(true);
	$barcode->setShowText(true);
	$barcode->setAutoAdjustFontSize(true);
	$barcode->setTextSpacing(5);
</pre>';
echo "<img src='3.png'>";
echo "<hr />";

$barcode = new phpCode128('124547657797', 150, 'arial.ttf', 18);
$barcode->setEanStyle(false);
$barcode->saveBarcode('4.png');
echo "<h3>Not using EAN style</h3>";
echo "<p>setEanStyle set to false</p>";
echo '<pre>
	$barcode->setBorderWidth(2);
	$barcode->setBorderSpacing(10);
	$barcode->setPixelWidth(2);
	$barcode->setEanStyle(false);
	$barcode->setShowText(true);
	$barcode->setAutoAdjustFontSize(true);
	$barcode->setTextSpacing(5);
</pre>';
echo "<img src='4.png'>";
echo "<hr />";

$barcode = new phpCode128('mikeleigh.com', 150, 'arial.ttf', 18);
$barcode->setEanStyle(false);
$barcode->setShowText(false);
$barcode->saveBarcode('5.png');
echo "<h3>Not using EAN style and hiding the text</h3>";
echo "<p>setEanStyle and setShowText set to false</p>";
echo '<pre>
	$barcode->setBorderWidth(2);
	$barcode->setBorderSpacing(10);
	$barcode->setPixelWidth(2);
	$barcode->setEanStyle(false);
	$barcode->setShowText(false);
	$barcode->setAutoAdjustFontSize(true);
	$barcode->setTextSpacing(5);
</pre>';
echo "<img src='5.png'>";
echo "<hr />";

$barcode = new phpCode128('mikeleigh.com', 150, 'arial.ttf', 18);
$barcode->setAutoAdjustFontSize(false);
$barcode->saveBarcode('6.png');
echo "<h3>Not using auto adjusting font size</h3>";
echo "<p>setAutoAdjustFontSize set to false</p>";
echo '<pre>
	$barcode->setBorderWidth(2);
	$barcode->setBorderSpacing(10);
	$barcode->setPixelWidth(2);
	$barcode->setEanStyle(true);
	$barcode->setShowText(true);
	$barcode->setAutoAdjustFontSize(false);
	$barcode->setTextSpacing(5);
</pre>';
echo "<img src='6.png'>";
echo "<hr />";

$barcode = new phpCode128('mikeleigh.com', 150, 'arial.ttf', 18);
$barcode->setTextSpacing(20);
$barcode->saveBarcode('7.png');
echo "<h3>Increase the text spacing</h3>";
echo "<p>setTextSpacing set to 20</p>";
echo '<pre>
	$barcode->setBorderWidth(2);
	$barcode->setBorderSpacing(10);
	$barcode->setPixelWidth(2);
	$barcode->setEanStyle(true);
	$barcode->setShowText(true);
	$barcode->setAutoAdjustFontSize(true);
	$barcode->setTextSpacing(20);
</pre>';
echo "<img src='7.png'>";
echo "<hr />";

?>