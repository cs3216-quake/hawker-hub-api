<?php

namespace HawkerHub\Models;

require_once('DatabaseConnection.php');

class PhotoModel extends \HawkerHub\Models\Model {

  public $photoId;
  public $photoUrl;
  public $userId;
  const COMPRESSION_RATE = 100;

  // Default constructor
  public function __construct($photoId, $photoUrl, $userId) {
    $this->photoId = $photoId;
    $this->photoUrl = $photoUrl;
    $this->userId = $userId;
  }

  public static function getFileNameFromUniqueId($uniqueId) {
    try {
      $db = \Db::getInstance();

      $req = $db->prepare('SELECT * FROM Photo WHERE uniqueId = :uniqueId;');
      $req->execute(array(
        'uniqueId' => $uniqueId
        ));

      $photo = $req->fetch();

      if (!$photo) {
        return null;
      }

      return $photo['fileName'];
    } catch (\PDOException $e) {
      return null;
    }
  }

  private static function saveToDatabase($fileName, $route, $userId) {
    try {
      $db = \Db::getInstance();

      $req = $db->prepare('INSERT INTO Photo (`fileName`, `uniqueId`, `userId`) VALUES (:fileName, :uniqueId, :userId);');
      $success = false;
      $uniqueId = "";
      while (!$success) {
        $uniqueId = md5(uniqid("", true));
        $success = $req->execute(array(
          'fileName' => $fileName,
          'userId' => $userId,
          'uniqueId' => $uniqueId
          ));
      }

      $id = $db->lastInsertId();

      if ($id > 0 && $success) {
        return new PhotoModel($id, $route . $uniqueId, $userId);
      }
      return null;
    } catch (\PDOException $e) {
      return null;
    }
  }

  public static function saveToFile($owner, $data, $mime, $dir, $route) {
    $id = md5(uniqid("", true));
    list($mime, $ext) = split("/", $mime);
    $name = "img-" . $id . '.' . $ext;

    //make sure filename is unique
    while (file_exists($dir.$name)) {
      $id = md5(uniqid("", true));
      $name = "img-" . $id . '.' . $ext;
    }

    $compressPath =   PhotoModel::compress($data,$dir . $name);
    PhotoModel::createThumbnail(512,400,$compressPath,$compressPath);

    if (file_exists($compressPath)) {
      return PhotoModel::saveToDatabase($name, $route, $owner);
    } else {
      return null;
    }
  }

  private static function createThumbnail($new_width,$new_height,$uploadDir,$moveToDir)
  {
    $path = $uploadDir;

    $mime = getimagesize($path);

    if($mime['mime']=='image/png'){ $src_img = imagecreatefrompng($path); }
    if($mime['mime']=='image/jpg'){ $src_img = imagecreatefromjpeg($path); }
    if($mime['mime']=='image/jpeg'){ $src_img = imagecreatefromjpeg($path); }
    if($mime['mime']=='image/pjpeg'){ $src_img = imagecreatefromjpeg($path); }

    $old_x          =   imageSX($src_img);
    $old_y          =   imageSY($src_img);

    $thumb_w    =   $new_width;
    $thumb_h    =   $old_y*($new_width/$old_x);
  

    $dst_img        =   ImageCreateTrueColor($thumb_w,$thumb_h);

    imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 


    // New save location
    $new_thumb_loc = $moveToDir;

    if($mime['mime']=='image/png'){ $result = imagepng($dst_img,$new_thumb_loc,100); }
    if($mime['mime']=='image/jpg'){ $result = imagejpeg($dst_img,$new_thumb_loc,100); }
    if($mime['mime']=='image/jpeg'){ $result = imagejpeg($dst_img,$new_thumb_loc,100); }
    if($mime['mime']=='image/pjpeg'){ $result = imagejpeg($dst_img,$new_thumb_loc,100); }

    if ($thumb_h > $new_height) {
      $to_crop_array = array('x' =>0 , 'y' => (($thumb_h-$new_height)/2.0), 'width' => $new_width, 'height'=> $new_height);
      $dst_img = imagecrop($dst_img, $to_crop_array);
      $result = imagejpeg($dst_img,$new_thumb_loc,100);
    }

    imagedestroy($dst_img); 
    imagedestroy($src_img);

    return $result;
  }

  private static function compress($source,$dest) {
   $info = getimagesize($source); 
   if ($info['mime'] == 'image/jpeg') {
     $image = imagecreatefromjpeg($source);
   } elseif ($info['mime'] == 'image/gif') {
     $image = imagecreatefromgif($source); 
   } elseif ($info['mime'] == 'image/png') {
     $image = imagecreatefrompng($source); 
   } 

   imagejpeg($image, $dest, PhotoModel::COMPRESSION_RATE); 
   imagedestroy($image);
   return $dest;
 }
}

