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
            if ($this->isFileValid($rawFiles['photoData'])) {

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
                $app->render(400, ['status' => 'File missing or not uploaded properly']);
            }
        } else {
            $app->render(401, array("Status" => "User not logged in"));
        }
    }

    private function isFileValid($file) {
        return ( isset($file) && $file['error'] == UPLOAD_ERR_OK );
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

        $fileName = PhotoModel::getFileNameFromUniqueId($uniqueId);

        $dir = 'uploads/';
        $fileUri = $dir . $fileName;
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $fileUri);
        finfo_close($finfo);
        if (file_exists($fileUri)) {
            header('Content-Disposition: inline; filename="'.basename($fileName).'"');
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
