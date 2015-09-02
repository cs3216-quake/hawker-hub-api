<?php

namespace HawkerHub\Models;

require_once('DatabaseConnection.php');

class PhotoModel extends \HawkerHub\Models\Model {

  public $photoId;
  public $photoUrl;
  public $userId;

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

    if (move_uploaded_file($data, $dir . $name) == true) {
      return PhotoModel::saveToDatabase($name, $route, $owner);
    } else {
      return null;
    }
  }
}

