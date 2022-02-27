<!DOCTYPE html>
<html lang="en">
  <head>
  <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImageCompressor</title>
	<link rel="shortcut icon" type="image" href="images/youtube.png">
	<link href="style.css" rel=stylesheet>
  </head>
	<body>
		<div class="row">
			<h3>Compress your image</h3>
			<div class="col-md-6 col-xs-12">
				<p>Note :</P>
				<ul>
					<li>Allowed image types are -- .jpg|.jpeg|.gif|.png.</li>
					<li>New width, height and quality are optional.</li>
					<li>New width and height should be greater than 0.</li>
					<li>Image quality range should in between 0-100 for .jpg and .gif images.</li>
					<li>Image quality range should in between 0-9 for .png images.</li>
				</ul> 
			</div>
			
			<div class="col-md-6 col-xs-12">
				<form method="post" enctype="multipart/form-data">
					<table width="500" border="0">
					  <tr>
						<td><label>Upload image <font color="#FF0000;">*</font></label><input id="file_1" type="file" name="uploadImg" value="" /></td>
					  </tr>
					  <tr>
						<td><label>New width</label><input type="text" name="width" value=""></td>
					  </tr>
					  <tr>
						<td><label>New height</label><input type="text" name="height" value=""></td>
					  </tr>
					  <tr>
						<td><label>Image quality</label><input type="text" name="quality" value=""></td>
					  </tr>
					  <tr>
						<td><input class="btn" type="submit" name="submit" value="Upload & Compress" /></td>
					  </tr>
					</table>
				</form>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-12">
				<?php
				$success = false;
				if(isset($_POST['submit']) && !empty($_POST['submit'])) {
					if(isset($_FILES['uploadImg']['name']) && @$_FILES['uploadImg']['name'] != "") {
						if($_FILES['uploadImg']['error'] > 0) {
							echo '<h4>Increase post_max_size and upload_max_filesize limit in php.ini file.</h4>';
						} else {
							if($_FILES['uploadImg']['size'] / 1024 <= 2048) { // 2MB
								if($_FILES['uploadImg']['type'] == 'image/jpeg' || 
								   $_FILES['uploadImg']['type'] == 'image/pjpeg' || 
								   $_FILES['uploadImg']['type'] == 'image/png' ||
								   $_FILES['uploadImg']['type'] == 'image/gif'){
									
									$source_file = $_FILES['uploadImg']['tmp_name'];
									$target_file = "uploads/compressed_" . $_FILES['uploadImg']['name']; 
									$width      = $_POST['width'];
									$height     = $_POST['height'];
									$quality    = $_POST['quality'];
									//$image_name = $_FILES['uploadImg']['name'];
									$success = compress_image($source_file, $target_file, $width, $height, $quality);
									if($success) {
										// Optional. The original file is uploaded to the server only for the comparison purpose.
										copy($source_file, "uploads/original_" . $_FILES['uploadImg']['name']);
									}
								}
							} else {
								echo '<h4>Image should be maximun 2MB in size!</h4>';
							}
						}
					} else {
						echo "<h4>Please select an image first!</h4>";
					}
				}
				?>
				
				<!-- Displaying original and compressed images -->
				<?php if($success) { ?>
				<?php $destination = "uploads/";?><span><?php echo"Image compressed successfully!!!"?></span>
				
				<a download="<?php echo $destination . "compressed_" . $_FILES['uploadImg']['name']?>"href="<?php echo $destination . "compressed_" . $_FILES['uploadImg']['name']?>" target="_blank" title="View actual size">Download</a><br>
				<?php } ?>
			</div>
			<footer>
    <div class="footer">
		<p class="text3">Like it!! Share it!!</p>
	   <p id="text">All uploaded data is deleted after 7 days.</p>
	   <p id="text2">pritangublu@gmail.com</p>
    </div>
</footer>
		</div>
			
	<!-- Compressing functions -->
<?php
function compress_image($source_file, $target_file, $nwidth, $nheight, $quality) {
	//Return an array consisting of image type, height, widh and mime type.
	$image_info = getimagesize($source_file);
	if(!($nwidth > 0)) $nwidth = $image_info[0];
	if(!($nheight > 0)) $nheight = $image_info[1];
	

	/*echo '<pre>';
	print_r($image_info);*/
	if(!empty($image_info)) {
		switch($image_info['mime']) {
			case 'image/jpeg' :
				if($quality == '' || $quality < 0 || $quality > 100) $quality = 75; //Default quality
				// Create a new image from the file or the url.
				$image = imagecreatefromjpeg($source_file);
				$thumb = imagecreatetruecolor($nwidth, $nheight);
				//Resize the $thumb image
				imagecopyresized($thumb, $image, 0, 0, 0, 0, $nwidth, $nheight, $image_info[0], $image_info[1]);
				// Output image to the browser or file.
				return imagejpeg($thumb, $target_file, $quality); 
				
				break;
			
			case 'image/png' :
				if($quality == '' || $quality < 0 || $quality > 9) $quality = 6; //Default quality
				// Create a new image from the file or the url.
				$image = imagecreatefrompng($source_file);
				$thumb = imagecreatetruecolor($nwidth, $nheight);
				//Resize the $thumb image
				imagecopyresized($thumb, $image, 0, 0, 0, 0, $nwidth, $nheight, $image_info[0], $image_info[1]);
				// Output image to the browser or file.
				return imagepng($thumb, $target_file, $quality);
				break;
				
			case 'image/gif' :
				if($quality == '' || $quality < 0 || $quality > 100) $quality = 75; //Default quality
				// Create a new image from the file or the url.
				$image = imagecreatefromgif($source_file);
				$thumb = imagecreatetruecolor($nwidth, $nheight);
				//Resize the $thumb image
				imagecopyresized($thumb, $image, 0, 0, 0, 0, $nwidth, $nheight, $image_info[0], $image_info[1]);
				// Output image to the browser or file.
				return imagegif($thumb, $target_file, $quality); //$success = true;
				break;
				
			default:
				echo "<h4>Not supported file type!</h4>";
				break;
		}
	}
}
?>
</body>
</html>