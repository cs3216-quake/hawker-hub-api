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
            if ($this->isFileValid($rawFiles['photoData'])) {
                $photoFile = $rawFiles['photoData'];
                $fileInfo = $this->createFileInfo($photoFile, $host);
                if (strcmp($fileInfo['TYPE'] , 'image') !== 0) {
                    $app->render(415, ['Status' => 'Inappropriate file format']);
                } else {
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

    public function isFileValid($file) {
        return ( isset($file) && $file['error'] == UPLOAD_ERR_OK );
    }

    public function createFileInfo($photoFile, $host){
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
