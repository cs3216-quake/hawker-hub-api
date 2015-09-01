<?php

namespace HawkerHub\Controllers;

use \HawkerHub\Models\LikeModel;

/**
 * Class ItemController
 *
 * Controller class for all retrieval and creation of food items`
 * @package HawkerHub
 **/
class LikeController extends \HawkerHub\Controllers\Controller {

    public function __construct() {
    }

    public function deleteLike($itemId) {
        $app = \Slim\Slim::getInstance();
        $userController = new \HawkerHub\Controllers\UserController();

        if ($userController->isLoggedIn()) {
            $success = LikeModel::deleteLike($itemId, $_SESSION['userId']);
            if (!$success) {
                $app->render(500, ['Status' => 'An error occured while removing like from item.' ]);
            } else {
                $app->render(204);
            }
        } else {
            $app->render(401, ['Status' => 'Not logged in.' ]);
        }
    }

    public function listLikes($itemId) {
        $app = \Slim\Slim::getInstance();
        $userController = new \HawkerHub\Controllers\UserController();
        $facebookFriendsId = $userController->getAllFacebookFriendsId();
        $ownUserId = @$_SESSION['userId']?$_SESSION['userId']:"";

        $likeData = LikeModel::findLikesByItem($itemId, $ownUserId, $facebookFriendsId);
        if (empty($likeData)) {
            $app->render(200, []);
        } else {
            $app->render(200, $likeData);

        }
    }

    public function insertLike($itemId){
        $userController = new \HawkerHub\Controllers\UserController();
        $app = \Slim\Slim::getInstance();
        
        if ($userController->isLoggedIn()) {

            if ($userController->canViewItem($itemId)) {
                $currUserId = $this->getCurrentUserId();    
                $result = LikeModel::addLikeByItemId($itemId, $currUserId);
                if($result) {
                    $app->render(200, array("Status" => "OK"));
                } else {
                    $app->render(500, array("Status" => "Unable to like item"));
                } 
            } else {
                $app->render(500, array("Status" => "Unable to like item"));
            }
        } else {
            $app->render(401, array("Status" => "User not logged in"));
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
