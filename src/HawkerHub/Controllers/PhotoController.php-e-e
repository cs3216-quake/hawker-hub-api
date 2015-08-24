<?php

namespace HawkerHub\Controllers;

use \HawkerHub\Models\PhotoModel;

/**
 * Class PhotoController
 *
 * Controller class for all retrieval and creation of food items`
 * @package HawkerHub
 **/
class PhotoController extends \HawkerHub\Controllers\Controller{

  public function __construct() {
        }

  public function uploadPhoto($host, $rawFile){
    $userController = new \HawkerHub\Controllers\UserController();
    $app = \Slim\Slim::getInstance();

    if ($userController->isLoggedIn()) {
      $currUserId = $this->getCurrentUserId();
      $dir = 'uploads/';
      if($rawFile['error'] != UPLOAD_ERR_OK) {
        $app->render(500, array("Status" => "File not uploaded properly"));
      } else {
        $route = $host . $app->urlFor('photo') .'/';
        $mime = $rawFile['type'];
        list($type, $ext) = split('/', $mime);
        if (strcmp($type , 'image') !== 0) {
          $app->render(401, ['Status' => 'Inappropriate file format']);
        } else {
          $result = PhotoModel::saveToFile($currUserId, $rawFile['tmp_name'], $mime, $dir, $route);
          if(is_null($result)) {
            $app->render(500, array("Status" => "Unable to save file"));
          } else {
            $app->render(200, array("Status" => "OK", "photoURL" => $result->photoUrl));
          }
        }
      }
    } else {
      $app->render(401, array("Status" => "User not logged in"));
    }
  }

  public function downloadPhoto($filename) {
    $app = \Slim\Slim::getInstance();
    $dir = 'uploads/';
    $fileUri = $dir . $filename;
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $fileUri);
    finfo_close($finfo);
    if (file_exists($fileUri)) {
      header('Content-Disposition: inline; filename="'.basename($filename).'"');
      header('Cache-Control: must-revalidate');
      header('Content-Length: ' . filesize($fileUri));
      $app->response()->header("Content-Type", $mime);
      readfile($fileUri);
    } else {
      $app->render(404);
    }
  }

  private function getCurrentUserId() {
    if (isset($_SESSION['userId'])) {
      return $_SESSION['userId'];
    } else {
      return null;
    }
  }
}
?>
