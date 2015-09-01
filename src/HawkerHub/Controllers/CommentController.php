<?php

namespace HawkerHub\Controllers;

use \HawkerHub\Models\CommentModel;

/**
 * Class CommentController
 *
 * Controller class for all retrieval and creation of food items`
 * @package HawkerHub
 **/
class CommentController extends \HawkerHub\Controllers\Controller {

    public function __construct() {
    }

    public function listComments($itemId) {
        $app = \Slim\Slim::getInstance();
        $userController = new \HawkerHub\Controllers\UserController();
        $facebookFriendsId = $userController->getAllFacebookFriendsId();
        $ownUserId = @$_SESSION['userId']?$_SESSION['userId']:"";

        $commentData = CommentModel::findCommentsByItem($itemId, $ownUserId, $facebookFriendsId);
        if (empty($commentData)) {
            $app->render(200, []);
        } else {
            $app->render(200, $commentData);
        }
    }

    public function deleteComment($itemId,$commentId) {
        $app = \Slim\Slim::getInstance();
        $userController = new \HawkerHub\Controllers\UserController();

        if ($userController->isLoggedIn()) {
            $success = CommentModel::deleteComment($itemId, $commentId, $_SESSION['userId']);
            if (!$success) {
                $app->render(500, ['Status' => 'An error occured while deleting item.' ]);
            } else {
                $app->render(204);
            }
        } else {
            $app->render(401, ['Status' => 'Not logged in.' ]);
        }
    }

    public function insertComment($itemId, $message){
        $app = \Slim\Slim::getInstance();
        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
        $userController = new \HawkerHub\Controllers\UserController();

        if ($userController->isLoggedIn()) {

            if ($userController->canViewItem($itemId)) {
                $currUserId = $this->getCurrentUserId();
                $result = CommentModel::addCommentByItem($itemId, $currUserId, $message);
                if($result) {
                    $app->render(200, array("Status" => "OK"));
                } else {
                    $app->render(500, array("Status" => "Unable to comment on item"));
                }
            } else {
                $app->render(500, array("Status" => "Unable to comment on item"));
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
