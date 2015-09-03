<?php

namespace HawkerHub\Controllers;

use \HawkerHub\Models\PhotoModel;

/**
 * Class PhotoController
 *
 * Controller class for all retrieval and creation of food items`
 * @package HawkerHub
 **/
class PhotoController extends \HawkerHub\Controllers\Controller {

    public function __construct() {
    }

    public function uploadPhoto($host, $rawFiles){
        $userController = new \HawkerHub\Controllers\UserController();
        $app = \Slim\Slim::getInstance();
        if ($userController->isLoggedIn()) {
            $currUserId = $this->getCurrentUserId();

            //Check if upload is successful
            if ($this->isFileValid($rawFiles)) {

                $photoFile = $rawFiles['photoData'];
                $fileInfo = $this->createFileInfo($photoFile, $host);
                $imageFileType = $fileInfo['EXTENSION'];
                //Make sure filetype is image 
                $check = getimagesize($photoFile['tmp_name']);
                if (strcmp($fileInfo['TYPE'] , 'image') !== 0 && $check === false && 
                    $imageFileType != "jpg" && $imageFileType != "png" 
                    && $imageFileType != "jpeg" && $imageFileType != "gif") {
                    $app->render(415, ['Status' => 'Inappropriate file format']);
                } else {
                        //  Save to directory and db

                    $result = PhotoModel::saveToFile($currUserId, $photoFile['tmp_name'], $fileInfo['MIME-TYPE'], $fileInfo['DIRECTORY'], $fileInfo['ROUTE']);
                    if(is_null($result)) {
                        $app->render(500, array("Status" => "Unable to save file"));
                    } else {
                        $app->render(200, array("Status" => "OK", "photoURL" => $result->photoUrl));
                    }
                }

            } else {
                $app->render(400, ['Status' => 'File missing or not uploaded properly']);
            }
        } else {
            $app->render(401, array("Status" => "User not logged in"));
        }
    }

    private function isFileValid($file) {
        return ( isset($file['photoData']) && $file['photoData']['error'] == UPLOAD_ERR_OK );
    }

    private function createFileInfo($photoFile, $host){
        $app = \Slim\Slim::getInstance();
        $info = array();
        $info['DIRECTORY'] = 'uploads/';
        $info['ROUTE'] = $host . $app->urlFor('photo') .'/';
        $info['MIME-TYPE'] = $photoFile['type'];
        list($type, $ext) = split('/', $info['MIME-TYPE']);
        $info['TYPE'] = $type;
        $info['EXTENSION'] = $ext;
        return $info;
    }

    public function downloadPhoto($uniqueId) {
        $app = \Slim\Slim::getInstance();

        if (empty($uniqueId) || is_null($uniqueId) || strlen($uniqueId) > 255 ) {
            $app->render(400, ['Status' => 'input is invalid.' ]);
            return;
        }

        $fileName = PhotoModel::getFileNameFromUniqueId($uniqueId);

        if (empty($fileName)) {
            $this->downloadPhoto('608aba97aabaabf9c136b781343caf4f');
            return;
        }
        $dir = 'uploads/';
        $fileUri = $dir . $fileName;
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $fileUri);
        finfo_close($finfo);
        if (file_exists($fileUri) && getimagesize($fileUri)) {
            $this->displayGraphicFile($app,$fileUri,$mime);
        } else {
            $this->downloadPhoto('608aba97aabaabf9c136b781343caf4f');
        }
    }

    private function displayGraphicFile ($app,$graphicFileName, $fileType='jpeg') {
      $fileModTime = filemtime($graphicFileName);
      // Getting headers sent by the client.
      $headers = $this->getRequestHeaders();
      // Checking if the client is validating his cache and if it is current.
      if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == $fileModTime)) {

        // Client's cache IS current, so we just respond '304 Not Modified'.
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', $fileModTime).' GMT', true, 304);
      } else {
        // Image not cached or cache outdated, we respond '200 OK' and output the image.
        header('Content-Disposition: inline; filename="'.basename($graphicFileName).'"');
        $app->response()->header("Content-Type", $fileType);
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', $fileModTime).' GMT', true, 200);
        header('Content-transfer-encoding: binary');
        header('Content-length: '.filesize($graphicFileName));
        readfile($graphicFileName);
      }
    }

    // return the browser request header
    // use built in apache ftn when PHP built as module,
    // or query $_SERVER when cgi
    private function getRequestHeaders() {
      if (function_exists("apache_request_headers")) {
        if($headers = apache_request_headers()) {
          return $headers;

        }
      }
      $headers = array();
      // Grab the IF_MODIFIED_SINCE header
      if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
        $headers['If-Modified-Since'] = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
      }
      return $headers;
    }

    private function getCurrentUserId() {
        if (isset($_SESSION['userId'])) {
            return $_SESSION['userId'];
        } else {
            return null;
        }
    }
}
