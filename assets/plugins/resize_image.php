<?php
     function getImageSizeKeepAspectRatio( $imageUrl, $maxWidth, $maxHeight){
    	$imageDimensions = getimagesize($imageUrl);
    	$imageWidth = $imageDimensions[0];
    	$imageHeight = $imageDimensions[1];
    	$imageSize['width'] = $imageWidth;
    	$imageSize['height'] = $imageHeight;
    	if($imageWidth > $maxWidth || $imageHeight > $maxHeight)
    	{
    		if ( $imageWidth > $imageHeight ) {
    	    	$imageSize['height'] = floor(($imageHeight/$imageWidth)*$maxWidth);
      			$imageSize['width']  = $maxWidth;
    		} else {
    			$imageSize['width']  = floor(($imageWidth/$imageHeight)*$maxHeight);
    			$imageSize['height'] = $maxHeight;
    		}
    	}
    	return $imageSize;
    }
     function getImageWithSpecificWidth( $imageUrl, $maxWidth){
    	$imageDimensions = getimagesize($imageUrl);
    	$imageWidth = $imageDimensions[0];
    	$imageHeight = $imageDimensions[1];
    	$imageSize['width'] = $imageWidth;
    	$imageSize['height'] = $imageHeight;
      $imageSize['height'] = floor(($imageHeight/$imageWidth)*$maxWidth);
      $imageSize['width']  = $maxWidth;
    	
    	return $imageSize;
    }
     function getImageWithSpecificHeight( $imageUrl, $maxHeight){
    	$imageDimensions = getimagesize($imageUrl);
    	$imageWidth = $imageDimensions[0];
    	$imageHeight = $imageDimensions[1];
    	$imageSize['width'] = $imageWidth;
    	$imageSize['height'] = $imageHeight;

      $imageSize['width']  = floor(($imageWidth/$imageHeight)*$maxHeight);
      $imageSize['height'] = $maxHeight;
    	return $imageSize;
    }

    function resize($file_name, $path, $width, $height, $center = false) {
    	/* Get original image x y*/
    	list($w, $h) = getimagesize($file_name);

    	/* calculate new image size with ratio */
    	$ratio = max($width/$w, $height/$h);
    	$h = ceil($height / $ratio);
    	$x = ($w - $width / $ratio) / 2;
    	$w = ceil($width / $ratio);
    	$y = 0;
    	if($center) $y = 250 + $h/1.5;

    	/* read binary data from image file */
    	$imgString = file_get_contents($file_name);

    	/* create image from string */
    	$image = imagecreatefromstring($imgString);
    	$tmp = imagecreatetruecolor($width, $height);
    	imagecopyresampled($tmp, $image,
    	0, 0,
    	$x, $y,
    	$width, $height,
    	$w, $h);

    	/* Save image */
    	imagejpeg($tmp, $path, 100);

    	return $path;
    	/* cleanup memory */
    	imagedestroy($image);
    	imagedestroy($tmp);
    }
?>
