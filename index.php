<?php
include "config.php";
//include "class.m_img.php"; 
$mode = 1;
$message = "";
if (extension_loaded('Imagick') && $c_debug_mode){
	$message = "Imagick Installed!";
}
if ($_GET['m'] == "v") // View Multiple Images
{
	$mode = 2;
	$n = $_GET['i'];
	$n = explode(',',$n);
}
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
		<script type="text/javascript">
		//<![CDATA[
			function getFileExtension(filename){var ext=/^.+\.([^.]+)$/.exec(filename);return ext==null?"":ext[1];}
			
			function foo(){
				$("#submit").html("Uploading...").attr("disabled","disabled").css("cursor", "auto").css('background', '#5d5d5d');
				var maxUploads=<?php echo $c_maxUploads;?>;
				var maxSize=<?php echo $c_maxFileSize;?>;
				var isInvalid=false;
				var message="";
				var inp=document.getElementById('Filedata');
				if (inp.files.length <=0){
					message = "Please select a file to upload!";
					isInvalid=true;
				}
				if(inp.files.length>maxUploads){
					message = "Only "+maxUploads+" files are allowed per upload!";
					isInvalid=true;
				}
				else{
					for(var i=0;i<inp.files.length;++i){
						var name=inp.files.item(i).name;
						var ext=getFileExtension(name);
                        ext = ext.toLowerCase();
						var size=inp.files.item(i).fileSize;
						if(ext!="png"&&ext!="jpeg"&&ext!="jpg"&&ext!="bmp"&&ext!="gif"){
							isInvalid=true;
							message = "Only images are allowed (png, jpeg, jpg, bmp, gif): "+name+"("+ext+" Not Supported)";
						}
						if(size>maxSize){
							isInvalid=true;
							message = "Files over "+maxSize+"MB are not allowed: "+name+"("+size+" Bytes)";
						}
					}
				}
				if(isInvalid){
					$("#submit").html("Upload...").removeAttr("disabled").css("cursor", "pointer").css('background', '#2d2d2d');
					alert(message);
					return false;
				}
				else{
					$("#form1").submit();
				}
			}

		//]]>
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
                <?php if ($mode != 1){?><li><a href="index.php">Upload</a></li><?php }?>
            </ul>
        </nav> 
        <div class="clear"></div>
    </header>    
    <div class="content">
<?php 
if ($mode == 1) {
?>
	<p>Select a file(s) and click <em>Upload...</em></p>
	
		<table class="table" style="width: 100%;">
			<tr><td>
			<form name="form1" id="form1" method="post" enctype="multipart/form-data"  action="up.php">
			<input type="file" class="file" name="Filedata[]" id="Filedata" accept="image/gif, image/jpeg, image/bmp, image/png" multiple>
			</form>
			</td></tr>
			
			<tr><td>
			<button name="Submit" id="submit" onclick="foo();">Upload...</button>
			</td></tr>
		</table>	
	
<?php
} 
?>
<?php 
if ($mode == 2) {?>
<?php
    $paUrl = $c_url."?m=v&i=";
    $z = 0;
	foreach($n as $f) 
	{
		$image = "i/".$f;
		$thumb = "t/".$f;
        $pUrl = $c_url."?m=v&i=".$f;
        if ($z != 0){
            $paUrl .= ",";
        }
        $paUrl .= $f;
        $z++;
		$retUrl = $c_url_formatted.$f;
?>
		<p><a href="<?php echo $image;?>"><img src="<?php echo $thumb;?>" alt="<?php echo $f;?>"></a></p>
		<p>Share this image:</p>
		<p>
            <a href="<?php echo $pUrl;?>">Permalink</a> |
			<a href="http://m.facebook.com/sharer.php?u=<?php echo $retUrl;?>">On Facebook</a> |
			<a href="http://mobile.twitter.com/?status=<?php echo $retUrl;?>">On Twitter</a>
		</p>
<?php 
	}

    if ($z > 1){
?>
<a href="<?php echo $paUrl;?>">Album Permalink</a>
<?php
    }
}
?>
<p><em>Contrary to popular belief TinyImg supports LARGE images*!</em></p>
<p>*Up to 10MB, 10 images per upload session.</p>
<p><?php echo $message; ?></p>
    </div>
	<footer>
		<p class="copy"><a href="admin.php">Admin Access</a></p>
        <p class="copy">&copy; 2014 Joe Jackson | All right reserved &bull;</p>
    </footer>
</body>
</html>