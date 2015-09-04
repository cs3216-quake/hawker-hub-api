<?php

namespace HawkerHub\Controllers;

use \HawkerHub\Models\CommentModel;
use \Facebook\Facebook;
use \Facebook\Exceptions\FacebookResponseException;

/**
 * Class CommentController
 *
 * Controller class for all retrieval and creation of food items`
 * @package HawkerHub
 **/
class CommentController extends \HawkerHub\Controllers\Controller {
    private $fb;
    public function __construct() {
        $this->fb = new Facebook([
            'app_id' => FB_APP_ID,
            'app_secret' => FB_SECRET,
            'cookie' => true
            ]);
    }

    public function listComments($itemId) {
        $app = \Slim\Slim::getInstance();
        $userController = new \HawkerHub\Controllers\UserController();

        if (!is_int(intval($itemId))) {
            $app->render(400, ['Status' => 'input is invalid.' ]);
            return;
        }

        if($userController->isLoggedIn()) {
            $facebookFriendsId = $userController->getAllFacebookFriendsId();
            $ownUserId = @$_SESSION['userId']?$_SESSION['userId']:"";

            $commentData = CommentModel::findCommentsByItem($itemId, $ownUserId, $facebookFriendsId);
            if (empty($commentData)) {
                $app->render(200, []);
            } else {
                $app->render(200, $commentData);
            }
        } else {
            $app->render(401, array("Status" => "User not logged in"));
        }
    }

    public function deleteComment($itemId,$commentId) {
        $app = \Slim\Slim::getInstance();
        $userController = new \HawkerHub\Controllers\UserController();

        if (!is_int(intval($itemId)) && !is_int(intval($commentId))) {
            $app->render(400, ['Status' => 'input is invalid.' ]);
            return;
        }

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

        if (!is_int(intval($itemId)) || empty(trim($message)) || is_null($message) || strlen($message) > 255 ) {
            $app->render(400, ['Status' => 'input is invalid.' ]);
            return;
        }

        $message = trim(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));
        $userController = new \HawkerHub\Controllers\UserController();

        if ($userController->isLoggedIn()) {

            if ($userController->canViewItem($itemId)) {
                $currUserId = $this->getCurrentUserId();
                $result = CommentModel::addCommentByItem($itemId, $currUserId, $message);
                if($result) {
                    try {
                        $response = $this->fb->POST(
                          'me/hawker-hub:commented_on',
                          array(
                            'food' => 'http://hawkerhub.quanyang.me/food/'.$itemId
                            ),
                          $_SESSION['fb_access_token']
                          );
                    } catch (FacebookResponseException $e) {
                    }
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
