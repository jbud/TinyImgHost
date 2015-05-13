<?php
/**
* m_img class v1.1 
* By Joe Jackson
* A powerful image uploader class.
* Created: 11/17/2012 - Part of P1xel project by Joe Jackson.
* Changes:
***** 11/17/2012 - 1.0
*	- Initial Release
***** 1/14/2014 - 1.1
*	- Support non-Imagick servers.
**/
class m_img
{
	var $m_temp;
	var $m_err;
    var $is_use_imagick;
	
	function m_img()
	{
		$this->m_temp = 0;
		$this->m_err = 0x0;
		if (!extension_loaded('imagick'))
			$this->is_use_imagick = false;
		else
			$this->is_use_imagick = true;
	}
	
	function m_refresh($url, $time = 1)
	{
		echo "<meta http-equiv='refresh' content='$time;url=$url'>";
	}
	
	function m_extension($filename) 
	{
		$pos = strrpos($filename, '.');
		if($pos === false) 
		{
			return false;
		}
		else
		{
			return substr($filename, $pos+1);
		}
	}
	function ImageCreateFromBMP($filename)
	{
		if (! $f1 = fopen($filename,"rb")) return FALSE;
		$FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));
		if ($FILE['file_type'] != 19778) return FALSE;
		$BMP = unpack(
			'Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
			'/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
			'/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40)
			);
		$BMP['colors'] = pow(2,$BMP['bits_per_pixel']);
		if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
		$BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
		$BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
		$BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
		$BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
		$BMP['decal'] = 4-(4*$BMP['decal']);
		if ($BMP['decal'] == 4) $BMP['decal'] = 0;
		$PALETTE = array();
		if ($BMP['colors'] < 16777216)
		{
			$PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
		}
		$IMG = fread($f1,$BMP['size_bitmap']);
		$VIDE = chr(0);

		$res = imagecreatetruecolor($BMP['width'],$BMP['height']);
		$P = 0;
		$Y = $BMP['height']-1;
		while ($Y >= 0)
		{
			$X=0;
			while ($X < $BMP['width'])
			{
				if ($BMP['bits_per_pixel'] == 24)
					$COLOR = unpack("V",substr($IMG,$P,3).$VIDE);
				elseif ($BMP['bits_per_pixel'] == 16)
				{  
					$COLOR = unpack("n",substr($IMG,$P,2));
					$COLOR[1] = $PALETTE[$COLOR[1]+1];
				}
				elseif ($BMP['bits_per_pixel'] == 8)
				{  
					$COLOR = unpack("n",$VIDE.substr($IMG,$P,1));
					$COLOR[1] = $PALETTE[$COLOR[1]+1];
				}
				elseif ($BMP['bits_per_pixel'] == 4)
				{
					$COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
					if (($P*2)%2 == 0) $COLOR[1] = ($COLOR[1] >> 4) ; else $COLOR[1] = ($COLOR[1] & 0x0F);
					$COLOR[1] = $PALETTE[$COLOR[1]+1];
				}
				elseif ($BMP['bits_per_pixel'] == 1)
				{
					$COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
					if     (($P*8)%8 == 0) $COLOR[1] =  $COLOR[1]        >>7;
					elseif (($P*8)%8 == 1) $COLOR[1] = ($COLOR[1] & 0x40)>>6;
					elseif (($P*8)%8 == 2) $COLOR[1] = ($COLOR[1] & 0x20)>>5;
					elseif (($P*8)%8 == 3) $COLOR[1] = ($COLOR[1] & 0x10)>>4;
					elseif (($P*8)%8 == 4) $COLOR[1] = ($COLOR[1] & 0x8)>>3;
					elseif (($P*8)%8 == 5) $COLOR[1] = ($COLOR[1] & 0x4)>>2;
					elseif (($P*8)%8 == 6) $COLOR[1] = ($COLOR[1] & 0x2)>>1;
					elseif (($P*8)%8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
					$COLOR[1] = $PALETTE[$COLOR[1]+1];
				}
				else
					return FALSE;
				imagesetpixel($res,$X,$Y,$COLOR[1]);
				$X++;
				$P += $BMP['bytes_per_pixel'];
			}
			$Y--;
			$P+=$BMP['decal'];
		}
		fclose($f1);

		return $res;
	}
	function m_thumbnail($image, $destination, $type)
	{
		if ($this->is_use_imagick){
			$thumb = new Imagick($image);

			$t_width = $thumb->getImageWidth();
			$t_height = $thumb->getImageHeight();

			$width = 250;

			$t_aspect = ($t_height > 0) ? $t_width / $t_height : 1;
			$height = ($t_aspect > 0) ? $width / $t_aspect : 150;

			$thumb->resizeImage($width,$height,Imagick::FILTER_POINT,1);
			$thumb->writeImage($destination);

			$thumb->clear();
			$thumb->destroy();
			return 1; // Success
		}
		else{
			if ($type=="gif") {
				$src = @imagecreatefromgif($image);
			}
			if ($type=="png") {
				$src = @imagecreatefrompng($image);
			}
			if ($type=="jpg") {
				$src = @imagecreatefromjpeg($image);
			}
			if ($type=="bmp")
			{
				$src = $this->ImageCreateFromBMP($image);	
			}
			
			$width = 250; // Width of thumbnail to be created.
			
			if ($src == false){
				return -1; // Exit here, we couldn't load the image.
			}
			
			$orig_x = imageSX($src);
			$orig_y = imageSY($src);
			
			$t_aspect = ($orig_y > 0) ? $orig_x / $orig_y : 1;
			$height = ($t_aspect > 0) ? $width / $t_aspect : 150;
			
			$temp = imagecreatetruecolor($width,$height); // Create new thumbnail sized image

			if ($type=="gif") { // Calculate transparency for gif images
				$transparente = imagecolortransparent($src);
				imagepalettecopy($src, $temp);
				imagefill($temp, 0, 0, $transparente);
				imagecolortransparent($temp, $transparente);
				imagetruecolortopalette($temp, true, 256);
			} else {
				imagecolortransparent($temp, imagecolorallocate($temp, 0, 0, 0) );
			}			

			imagealphablending($temp, false);
			imagesavealpha($temp, true);

			imagecopyresampled($temp,$src,0,0,0,0,$width,$height,$orig_x,$orig_y);
			
			if ($type=="gif") { imagegif($temp, $destination); }
			if ($type=="png") { imagepng($temp, $destination); }
			if ($type=="jpg") { imagejpeg($temp, $destination, 86); } // Use slightly lower quality for jpeg, as they tend to be huge.
			if ($type=="bmp") { imagepng($temp, $destination); } // We will save the bmp files as png. They will also be converted upon upload.

			imagedestroy($temp); 
			imagedestroy($src);	
			return 1;
		}
	}
	
	function m_rename($name)
	{
		$ext = $this->m_extension($name);
		if ($ext=="peg" || $ext == "jpeg") { $ext = 'jpg'; }
		$n = explode(".".$ext, $name);
		$name = $n[0];
		$name = strtolower($name);
		$name = preg_replace("/[^[:alnum:]]/","",$name);
		$ch_1 = chr(rand(ord("a"), ord("z")));
		$ch_2 = chr(rand(ord("z"), ord("a")));
		$ch_3 = chr(rand(ord("z"), ord("a")));
		$len = strlen($name);
		if ($len > 10)
		{
			$name = substr("$name", 0, 10);
		}
		else
		{
			if (empty($name)) {
				$name = $ch_1.$ch_2.$ch_3;
			}
			else 
			{
				$name = $name;
			}
		}
		$name = $name.$ch_1.$ch_2.$ch_3.'.'.$ext;
		return $name;
	}
}
?>