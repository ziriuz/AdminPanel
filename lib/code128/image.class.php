<?php
class image {

	var $image;
	var $status = true;
	var $width;
	var $height;

	var $colours = array();
	var $fonts = array();

	var $error = array();

	function image($width, $height) {
		$this->setWidth($width);
		$this->setHeight($height);
		$this->createImage();
	}

	function drawImage() {
		$image = &$this->image;
		if($this->getStatus()) {
			header('Content-Type: image/png');
			imagePNG($image);
		}
	}

	function saveImage($filename) {
		$image = &$this->image;
		if($this->getStatus()) {
			imagePNG($image, $filename);
		}
	}

	function createImage() {
		//creates the base image canvas and fills the image with white with no transparency
		$image = &$this->image;
		$width = $this->getWidth();
		$height = $this->getHeight();
		//check for invalud height
		if($height <= 0) {
			$this->setError('createImage: image height must be greater than 0');
		}
		//check for invalid width
		if($width <= 0) {
			$this->setError('createImage: image width must be greater than 0');
		}
		if($this->getStatus()) {
			$this->image = imagecreatetruecolor($width, $height);
			$colour = $this->setColour('ffffff00');
			imagefill($image, 0, 0, $colour);
		}
	}

	function getWidth() {
		return $this->width;
	}

	function setWidth($width) {
		$this->width = $width;
	}

	function getHeight() {
		return $this->height;
	}

	function setHeight($height) {
		$this->height = $height;
	}

	function getColour($hex) {
		$colour = &$this->colours;
		if(isset($colour[strtolower($hex)])) {
			return $colour[strtolower($hex)];
		}
		return false;
	}

	function setColour($string) {
		$hex = strtolower($string);
		$colour = &$this->colours;
		$res_colour = $this->getColour($hex);
		if($res_colour == false) {
			$image = &$this->image;
			$rgb = array('r' => hexdec(substr($hex, 0, 2)), 'g' => hexdec(substr($hex, 2, 2)), 'b' => hexdec(substr($hex, 4, 2)), 'a' => hexdec(substr($hex, 6, 2)));
			$colour[$hex] = imagecolorallocatealpha($image, $rgb['r'], $rgb['g'], $rgb['b'], $rgb['a']);
			$res_colour = $colour[$hex];
		}
		return $res_colour;
	}

	function drawPixel($x, $y, $colour) {
		$image = &$this->image;
		$width = $this->getWidth();
		$height = $this->getHeight();
		if(($x < 0) and ($x > $width)) {
			$this->setError('drawPixel: the value '.$x.' for x must be between 0 and '.$width);
		}
		if(($y < 0) and ($y > $width)) {
			$this->setError('drawPixel: the value '.$y.' for y must be between 0 and '.$height);
		}
		$res_colour = $this->setColour($colour);
		if($this->getStatus()) {
			imagesetpixel($image, $x, $y, $res_colour);
		}
	}

	function drawLine($x1, $y1, $x2, $y2, $thickness, $colour) {
		$image = &$this->image;
		$width = $this->getWidth();
		$height = $this->getHeight();
		if(($x1 < 0) and ($x1 > $width)) {
			$this->setError('drawLine: the value '.$x1.' for x1 must be between 0 and '.$width);
		}
		if(($x2 < 0) and ($x2 > $width)) {
			$this->setError('drawLine: the value '.$x2.' for x2 must be between 0 and '.$width);
		}
		if(($y1 < 0) and ($y1 > $height)) {
			$this->setError('drawLine: the value '.$y1.' for y1 must be between 0 and '.$height);
		}
		if(($y2 < 0) and ($y2 > $height)) {
			$this->setError('drawLine: the value '.$y2.' for y2 must be between 0 and '.$height);
		}
		$res_colour = $this->setColour($colour);
		if($this->getStatus()) {
			imagesetthickness($image, $thickness);
			imageline($image, $x1, $y1, $x2, $y2, $res_colour);
		}
	}

	function drawRectangle($x1, $y1, $x2, $y2, $thickness, $colour) {
		$image = &$this->image;
		$width = $this->getWidth();
		$height = $this->getHeight();
		if(($x1 < 0) and ($x1 > $width)) {
			$this->setError('drawLine: the value '.$x1.' for x1 must be between 0 and '.$width);
		}
		if(($x2 < 0) and ($x2 > $width)) {
			$this->setError('drawLine: the value '.$x2.' for x2 must be between 0 and '.$width);
		}
		if(($y1 < 0) and ($y1 > $height)) {
			$this->setError('drawLine: the value '.$y1.' for y1 must be between 0 and '.$height);
		}
		if(($y2 < 0) and ($y2 > $height)) {
			$this->setError('drawLine: the value '.$y2.' for y2 must be between 0 and '.$height);
		}
		$res_colour = $this->setColour($colour);
		if($this->getStatus()) {
			imagesetthickness($image, $thickness);
			$ct = ceil($thickness / 2);
			$ft = floor($thickness / 2);
			imageline($image, ($x1 - $ft), $y1, ($x2 - $ct), $y1, $res_colour);
			imageline($image, $x1, ($y1 + $ct), $x1, ($y2 + $ft), $res_colour);
			if($ct == $ft) {
				imageline($image, ($x1 + $ct), ($y2 + 1), ($x2 + $ft), ($y2 + 1), $res_colour);
				imageline($image, ($x2 + 1), ($y1 - $ft), ($x2 + 1), ($y2 - $ct), $res_colour);
			} else {
				imageline($image, ($x1 + $ct), $y2, ($x2 + $ft), $y2, $res_colour);
				imageline($image, $x2, ($y1 - $ft), $x2, ($y2 - $ct), $res_colour);
			}
		}
	}

	function drawFilledRectangle($x1, $y1, $x2, $y2, $colour) {
		$image = &$this->image;
		$width = $this->getWidth();
		$height = $this->getHeight();
		if(($x1 < 0) and ($x1 > $width)) {
			$this->setError('drawFilledRectangle: the value '.$x1.' for x1 must be between 0 and '.$width);
		}
		if(($x2 < 0) and ($x2 > $width)) {
			$this->setError('drawFilledRectangle: x2 must be less then the image width');
		}
		if(($y1 < 0) and ($y1 > $width)) {
			$this->setError('drawFilledRectangle: y1 must be less then the image height');
		}
		if(($y2 < 0) and ($y2 > $width)) {
			$this->setError('drawFilledRectangle: y2 must be less then the image height');
		}
		if($x1 > $x2) {
			$this->setError('drawFilledRectangle: the value '.$x1.' for x1 must be less than the value '.$x2.' for x2');
		}
		if($y1 > $y2) {
			$this->setError('drawFilledRectangle: the value '.$y1.' for y1 must be less than the value '.$y2.' for y2');
		}
		$res_colour = $this->setColour($colour);
		if($this->getStatus()) {
			imagefilledrectangle($image, $x1, $y1, $x2, $y2, $res_colour);
		}
	}

	function drawFilledBorderedRectangle($x1, $y1, $x2, $y2, $thickness, $linecolour, $fillcolour) {
		$image = &$this->image;
		$width = $this->getWidth();
		$height = $this->getHeight();
		if(($x1 < 0) and ($x1 > $width)) {
			$this->setError('drawFilledBorderedRectangle: the value '.$x1.' for x1 must be between 0 and '.$width);
		}
		if(($x2 < 0) and ($x2 > $width)) {
			$this->setError('drawFilledBorderedRectangle: the value '.$x2.' for x2 must be between 0 and '.$width);
		}
		if(($y1 < 0) and ($y1 > $width)) {
			$this->setError('drawFilledBorderedRectangle: the value '.$y1.' for y1 must be between 0 and '.$height);
		}
		if(($y2 < 0) and ($y2 > $width)) {
			$this->setError('drawFilledBorderedRectangle: the value '.$y2.' for y2 must be between 0 and '.$height);
		}
		if($x1 > $x2) {
			$this->setError('drawFilledBorderedRectangle: the value '.$x1.' for x1 must be less than the value '.$x2.' for x2');
		}
		if($y1 > $y2) {
			$this->setError('drawFilledBorderedRectangle: the value '.$y1.' for y1 must be less than the value '.$y2.' for y2');
		}
		if($this->getStatus()) {
			$ct = ceil($thickness / 2);
			$ft = floor($thickness / 2);
			$modifier = 0;
			if($thickness > 0) {
				$modifier = 1;
				if($thickness == 2) {
					$this->drawRectangle(($x1 + $ft), ($y1 + $ft), ($x2 - $ct) - 1, ($y2 - $ct) - 1, $thickness, $linecolour);
				} else {
					$this->drawRectangle(($x1 + $ft), ($y1 + $ft), ($x2 - $ct), ($y2 - $ct), $thickness, $linecolour);
				}
			}
			if(($ct == $ft) and ($ct >= 2) and ($ft >= 2)) {
				$modifier--;
				$this->drawFilledRectangle(($x1 + $thickness), ($y1 + $thickness), ($x2 - $thickness) - $modifier, ($y2 - $thickness) - $modifier, $fillcolour);
			} else {
				$this->drawFilledRectangle(($x1 + $thickness), ($y1 + $thickness), ($x2 - $thickness) - $modifier, ($y2 - $thickness) - $modifier, $fillcolour);
			}
		}
	}

	function getStatus() {
		return $this->status;
	}

	function setStatus($boolean) {
		$this->status = $boolean;
	}

	function setError($message) {
		$this->error[] = $message;
		$this->setStatus(false);
	}























	function drawText($x, $y, $angle, $font, $fontsize, $colour, $text) {
		$image = &$this->image;
		$colour = $this->setColour($colour);
		imagettftext($image, $fontsize, $angle, $x, $y, $colour, $font, $text);
	}

	function addFont($font) {
		$index = $this->arraySearch($font, $this->fonts);
		if($index == -1) {
			$this->fonts[] = $font;
			$index = (count($this->fonts) - 1);
		}
		return $index;
	}

	function arraySearch($needle, &$haystack) {
		for($i = 0; $i <= count($haystack) - 1; $i++) {
			if($haystack[$i] == $needle) {
				return $i;
			}
		}
		return -1;
	}

	function isHex($string) {
		$hex = '0123456789abcdef';
		if(strlen($string) == 0) {
			return false;
		}
		$temp = $string;
		for($i = 0; $i <= strlen($hex) - 1; $i++) {
			$temp = str_replace($hex{$i}, '', $temp);
		}
		if(strlen($temp) == 0) {
			return true;
		}
		return false;
	}
}
?>