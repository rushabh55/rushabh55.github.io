<?php
    Header("Content-type: image/png");
class textPNG 
{
	var $font = 'Starjedi.ttf'; //default font. directory relative to script directory.
	var $msg = "no text"; // default text to display.
	var $size = 24; // default font size.
	var $rot = 0; // rotation in degrees.
	var $pad = 0; // padding.
	var $transparent = 1; // transparency set to on.
	var $red = 0; // black text...
	var $grn = 0;
	var $blu = 0;
	var $bg_red = 255; // on white background.
	var $bg_grn = 255;
	var $bg_blu = 255;
	
	function draw() 
	{
		$width = 0;
		$height = 0;
		$offset_x = 0;
		$offset_y = 0;
		$bounds = array();
		$image = "";
	
		// get the font height.
		$bounds = ImageTTFBBox($this->size, $this->rot, $this->font, "W");
		if ($this->rot < 0) 
		{
			$font_height = abs($bounds[7]-$bounds[1]);		
		} 
		else if ($this->rot > 0) 
		{
		$font_height = abs($bounds[1]-$bounds[7]);
		} 
		else 
		{
			$font_height = abs($bounds[7]-$bounds[1]);
		}
		// determine bounding box.
		$bounds = ImageTTFBBox($this->size, $this->rot, $this->font, $this->msg);
		if ($this->rot < 0) 
		{
			$width = abs($bounds[4]-$bounds[0]);
			$height = abs($bounds[3]-$bounds[7]);
			$offset_y = $font_height;
			$offset_x = 0;
		} 
		else if ($this->rot > 0) 
		{
			$width = abs($bounds[2]-$bounds[6]);
			$height = abs($bounds[1]-$bounds[5]);
			$offset_y = abs($bounds[7]-$bounds[5])+$font_height;
			$offset_x = abs($bounds[0]-$bounds[6]);
		} 
		else
		{
			$width = abs($bounds[4]-$bounds[6]);
			$height = abs($bounds[7]-$bounds[1]);
			$offset_y = $font_height;;
			$offset_x = 0;
		}
		
		$image = imagecreate($width+($this->pad*2)+1,$height+($this->pad*2)+1);
		$background = ImageColorAllocate($image, $this->bg_red, $this->bg_grn, $this->bg_blu);
		$foreground = ImageColorAllocate($image, $this->red, $this->grn, $this->blu);
	
		if ($this->transparent) ImageColorTransparent($image, $background);
		ImageInterlace($image, false);
	
		// render the image
		ImageTTFText($image, $this->size, $this->rot, $offset_x+$this->pad, $offset_y+$this->pad, $foreground, $this->font, $this->msg);
	
		// output PNG object.
		imagePNG($image);
		}
	}

	$text = new textPNG();
    $msg = $_GET['msg'];
    $size = $_GET['size'];
    $rot = $_GET['rot'];
    $pad = $_GET['pad'];
    $red = $_GET['red'];
    $grn = $_GET['grn'];
    $blu = $_GET['blu'];

    $bg_red = $_GET['bg_red'];
    $bg_grn = $_GET['bg_grn'];
    $bg_blu = $_GET['bg_blu'];
    $font = $_GET['font'];
    $fontPre = "fonts.googleapis.com/css?family=";

    $tr = $_GET['tr'];
	if (isset($msg)) $text->msg = $msg; // text to display
	if (isset($font)) $text->font = $font; // font to use (include directory if needed).
	if (isset($size)) $text->size = $size; // size in points
	if (isset($rot)) $text->rot = $rot; // rotation
	if (isset($pad)) $text->pad = $pad; // padding in pixels around text.
	if (isset($red)) $text->red = $red; // text color
	if (isset($grn)) $text->grn = $grn; // ..
	if (isset($blu)) $text->blu = $blu; // ..
	if (isset($bg_red)) $text->bg_red = $bg_red; // background color.
	if (isset($bg_grn)) $text->bg_grn = $bg_grn; // ..
	if (isset($bg_blu)) $text->bg_blu = $bg_blu; // ..
	if (isset($tr)) $text->transparent = $tr; // transparency flag (boolean).
	if (isset($font)) $text->font = $fontPre . $font; //The Font! Google APIs only supported!
	$text->draw(); // GO!!!!!
?>
    