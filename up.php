<?php
include "config.php";
include "class.m_img.php";
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <title><?php echo $c_title." - ".$c_tagline;?></title>
        <link href="style.css" rel="stylesheet" type="text/css" />
        <meta name="keywords" content="mobile upload images img tinyimghost tiny host" />
        <meta name="description" content="TinyImgHost is an open-sourced image host made for mobile. its simple and fast." />
		<script type="text/javascript">
		</script>
	</head>
<body>
<header>
        <div class="logo">
            <a href="index.php">
                <h2><?php echo $c_title;?> - <span class="tag"><?php echo $c_tagline;?></span></h2>
            </a>
        </div>
		<nav class="vertical menu">
            <ul>
                <?php if (false){?><li><a href="index.php">Upload</a></li><?php }?>
            </ul>
        </nav> 
        <div class="clear"></div>
    </header>
	<div class="content"><p>
<?php
//error_reporting(0);

if (!isset($_FILES["Filedata"]))
{
	echo "ERROR:invalid upload";
	exit(0); // Exit early, we don't have any file uploads, this page can only be opened via index.php
}

$message = array();
$names = "";
$m_img = new m_img;

foreach ($_FILES['Filedata']['name'] as $i => $name)
{
	// Check for unknown errors:
	if (!is_uploaded_file($_FILES["Filedata"]["tmp_name"][$i]) || $_FILES["Filedata"]["error"][$i] != 0)
	{
		$message[] = "ERROR 1: Unknown Error... ".$_FILES["Filedata"]["error"][$i];
	}

	// Set variables:
	$m_img->m_name = $_FILES["Filedata"]["name"][$i];
	$m_img->m_temp = "temp/".$m_img->m_name;
	
	// Create temporary image:
	copy($_FILES["Filedata"]["tmp_name"][$i], $m_img->m_temp);

	// Init imagick object:
	if ($m_img->is_use_imagick)
		$img = new Imagick($m_img->m_temp);
	else
		$img = null;

	// Get filetype:
	$ext = $m_img->m_extension($m_img->m_name);
	echo $ext;
	$ext = strtolower($ext);
	
	// Check for proper formats:
	if ($ext != "bmp" && $ext != "jpg" && $ext != "jpeg" && $ext != "png" && $ext != "gif")
	{
		$message[] = "ERROR 2: Invalid File Type... ".$m_img->m_name."(".$ext.")"; // This is only thrown if javascript filter fails.
	}
	
	// If the image is BMP, convert it to PNG:
	if ($ext == "bmp")
	{
		$tmpold = $m_img->m_temp;
		$t_name = explode(".".$ext, $m_img->m_temp);
		$m_img->m_temp = $t_name[0].".png";
		$t_name = explode(".".$ext, $m_img->m_name);
		$m_img->m_name = $t_name[0].".png";
		rename($tmpold, $m_img->m_temp);
		if ($m_img->is_use_imagick){			
			$img->setImageFormat("png");
			$img->writeImage($m_img->m_temp);
			$img->clear();
			$img->destroy();
		}
		else{
			$src = $m_img->ImageCreateFromBMP($target);
			$orig_x = imageSX($src);
			$orig_y = imageSY($src);
			$temp = imagecreatetruecolor($orig_x,$orig_y);
			imagealphablending($temp, false);
			imagesavealpha($temp, true);

			imagecopyresampled($temp,$src,0,0,0,0,$orig_x,$orig_x,$orig_x,$orig_y);
			imagepng($temp, $m_img->m_temp);
			imagedestroy($temp); 
			imagedestroy($src);
		}
	}

	// This loop will rename a file until the name is not taken, it will also cut down a long filename:
	do{
		$m_img->m_name = $m_img->m_rename($m_img->m_name);
	}while(file_exists("i/".$m_img->m_name)); // Cleverly ensure that rename is called once and only calls again if in the slight chance we randomly generate a taken name.
	
	// Add name to list of names:
	$names .= (empty($names)) ? $m_img->m_name : ",".$m_img->m_name;

	// Set path variables here, instead of above due to m_rename's effect:
	$path2destImg = "i/".$m_img->m_name;
	$path2destThm = "t/".$m_img->m_name;
	
	// Copy temp image to destination.
	copy($m_img->m_temp, $path2destImg);
	
	$d = exif_imagetype($m_img->m_temp);
	if ($c_debug_mode)
		var_dump($d);
	// Create thumbnail:
	$r = $m_img->m_thumbnail($path2destImg, $path2destThm, $ext);
	if ($c_debug_mode)
		var_dump($r);
	unlink($m_img->m_temp);
}
if (!empty($message))
{
	echo "An error(s) occurred:<br/>";
	foreach ($message as $m)
	{
		if ($c_debug_mode)
			echo $m."</br>";
	}
	exit(0);
}
$m_img->m_refresh("index.php?m=v&i=".$names);
?></p>
<h4>Please Wait...</h4>
</div>
<footer>
        <p class="copy">&copy; 2014 Joe Jackson | All right reserved &bull;</p>
    </footer>
</body>
</html>