<?php

namespace HawkerHub\Models;

require_once('DatabaseConnection.php');

class PhotoModel extends \HawkerHub\Models\Model{

  public $photoId;
  public $photoUrl;
  public $userId;

  // Default constructor
  public function __construct($photoId, $photoUrl, $userId) {
    $this->photoId = $photoId;
    $this->photoUrl = $photoUrl;
    $this->userId = $userId;
  }

  public function saveToFile($owner, $data, $mime, $dir, $route){
    $id = md5(uniqid("", true));
    list($mime, $ext) = split("/", $mime); 
    $name = "img-" . $id . '.' . $ext;
    if (move_uploaded_file($data, $dir . $name) == true) {
      return new PhotoModel($id, $route . $name, $owner);
    } else {
      return null;
    }
  }
}

?>
