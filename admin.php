<?php
include "config.php";
include "class.m_img.php";


$validToken = "420noscope4yeezus"; // admin.php?token=420noscope4yeezus
if ($_GET['token'] != $validToken){
	die("Sorry dog.");
}

$tokn = $_GET['token'];
$m_img = new m_img;
function getAllImages(){
	$m_img = new m_img;
	$dir = scandir("i");
	$dir = array_diff($dir, array('..', '.'));
	$builtArray = array();
	foreach ($dir as $file)
	{
		$ext = $m_img->m_extension("i/".$file);
		$ext = strtolower($ext);
		if ($ext == 'png' || $ext == "jpg" || $ext == "gif" || $ext == "jpeg"){
			$builtArray[] = "i/".$file;
		}
	}
	return $builtArray;
}
$builtHTML = "<tr>";

if ($_GET["manage"] == 1){
	$i = $_GET["image"];
	$manage = base64_encode($i);
	$builtHTML .= "
	<th><p><img width='650' alt='{$i}' src='{$c_url}{$i}'/></p>
	<p><a href='?token={$tokn}&manage=2&function=1&image={$manage}'>Temp Ban for 1 day</a></p>
	<p><a href='?token={$tokn}&manage=2&function=2&image={$manage}'>Temp Ban for 1 week</a></p>
	<p><a href='?token={$tokn}&manage=2&function=3&image={$manage}'>Delete</a></p></th>
	";
	
	
}elseif ($_GET["manage"] == 2){
	$i = base64_decode($_GET["image"]);
	switch($_GET["function"]){
		case 1:
			break;
		case 2:
			break;
		case 3:
			if (unlink($c_path.$i)){
				$builtHTML .= "<th><h2>successfully deleted {$i}...</h2></th>";
				$m_img = new m_img;
				$m_img->m_refresh("admin.php?token={$tokn}");
			}else{
				$builtHTML .= "<th><h2>an error has occurred while deleting {$i}...</h2></th>";
			}
			break;
		default:
			break;
	}
}else{
	$images = getAllImages();
	$j=0;
	foreach($images as $i){
		if ($j == 6){
			$j = 0;
			$builtHTML .= "</tr><tr>";
		}
		$builtHTML .= "
		<th><p><img style='height:300px;max-width:300px;width: expression(this.width > 300 ? 300: true);' src='{$c_url}{$i}' alt='{$i}' width='300'/></p>
		<p><a href='?token=420noscope4yeezus&manage=1&image={$i}'>manage this image...</a></p></th>
		";
		$j++;
	}
}
$builtHTML .= "</tr>"

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <title><?php echo $c_title." - ".$c_tagline;?></title>
        <link href="style.css" rel="stylesheet" type="text/css" />
        <meta name="keywords" content="mobile upload images img tinyimghost tiny host" />
        <meta name="description" content="TinyImgHost is an open-sourced image host made for mobile. its simple and fast"/>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
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
                <li><a href="admin.php?token=<?php echo $tokn;?>">Admin</a></li>
				<li><a href="index.php">Upload</a></li>
            </ul>
        </nav> 
        <div class="clear"></div>
    </header>
	<div class="content">
		<table style="margin:auto;display: table;border-collapse: separate;border-spacing: 2px;border-color: gray;">
			<?php echo $builtHTML;?>
		</table>
	</div>
	<footer>
        <p class="copy">&copy; 2014 Joe Jackson | All right reserved &bull;</p>
    </footer>
</body>
</html>