<?php
include "config.php";
include "class.m_img.php";


$validToken = "420noscope4yeezus"; // admin.php?token=420noscope4yeezus
if ($_GET['token'] != $validToken){
	die("Sorry dog.");
}
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
                <?php if ($mode != 1){?><li><a href="index.php">Upload</a></li><?php }?>
            </ul>
        </nav> 
        <div class="clear"></div>
    </header>
	<div class="content">
		<table style="display: table;border-collapse: separate;border-spacing: 2px;border-color: gray;">
			<?php echo $builtHTML;?>
		</table>
	</div>
	<footer>
        <p class="copy">&copy; 2014 Joe Jackson | All right reserved &bull;</p>
    </footer>
</body>
</html>