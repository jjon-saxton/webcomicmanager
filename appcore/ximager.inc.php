<?php

if (!function_exists('imagecreatefromstring'))
{
 trigger_error ("GD Library is required in order to use WebComiX's Cross Image Parser!",E_USER_NOTICE);
 exit();
}

class MCImage
{
 public $filename;
 private $image;
 private $imageinfo=array();

 public function __construct($filename)
 {
  $this->filename=$filename;
  $data=file_get_contents($filename);
  $image=imagecreatefromstring($data);

  $this->imageinfo['width']=imagesx($image);
  $this->imageinfo['height']=imagesy($image);
  $this->image=$image;
 }

 public function __get($key)
 {
  if (array_key_exists($key,$this->imageinfo))
  {
   return $this->imageinfo[$key];
  }
  else
  {
   return null;
  }
 }

 public function get($quality=9)
 {
  ob_start();
  imagepng($this->image,null,$quality);
  return ob_get_clean();
 }
 
 public function save($loc,$quality=9)
 {
   if (imagepng($this->image,$loc,$quality))
   {
     return $loc;
   }
   else
   {
     return false;
   }
 }

 public function isImage()
 {
  if ($this->height > 0)
  {
   return true;
  }
  else
  {
   return false;
  }
 }

 public function resizeToHeight($height)
 {
  $ratio=$height/$this->height;
  $width=$this->width*$ratio;
  $this->resize($width,$height);
 }

 public function resizeToWidth($width)
 {
  $ratio=$width/$this->width;
  $height=$this->height*$ratio;
  $this->resize($width,$height);
 }

 public function scale($percent)
 {
  $width=$this->width*($percent/100);
  $height=$this->height*($percent/100);
  $this->resize($width,$height);
 }

 public function resize($width,$height,$tomax=true)
 {
  if ($tomax == TRUE && ($width < $this->imageinfo['width'] || $height < $this->imageinfo['height']))
  {
    $oldimage=$this->image;
    
    if ($tomax && $width > $this->imageinfo['width'])
    {
      $width=$this->imageinfo['width'];
    }
    if ($tomax && $height > $this->imageinfo['height'])
    {
     $heigh=$this->imageinfo['height'];
    }
    
    $newimage=imagecreatetruecolor($width,$height);
    imagealphablending($newimage, false);
    imagesavealpha($newimage, true);
    $transparent=imagecolorallocatealpha($newimage, 255, 255, 255, 127);
    imagefilledrectangle($newimage, 0, 0, $width, $height, $transparent);
    imagecopyresampled($newimage,$oldimage,0,0,0,0,$width,$height,$this->width, $this->height);

    $this->imageinfo['width']=imagesx($newimage);
    $this->imageinfo['height']=imagesy($newimage);
    $this->image=$newimage;
  }
 }
 
 public function __destruct()
 {
  imagedestroy($this->image);
 }
}