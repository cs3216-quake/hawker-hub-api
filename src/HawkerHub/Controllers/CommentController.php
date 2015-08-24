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
    $commentData = CommentModel::findCommentsByItem($itemId);
    $result = array();
    if (empty($commentData)) {
      $result['total'] = 0;
      $result['collection'] = [];
      $app->render(200, $result);
    } else {
      $result['total'] = count($commentData);
      $result['collection'] = $commentData;
      $app->render(200, $result);
    }
  }

  public function insertComment($itemId, $message){
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
  $userController = new \HawkerHub\Controllers\UserController();

    if ($userController->isLoggedIn()) {
      $currUserId = $this->getCurrentUserId();
    $app = \Slim\Slim::getInstance();
    $result = CommentModel::addCommentByItem($itemId, $currUserId, $message);
    if($result) {
      $app->render(200, array("Status" => "OK"));
    } else {
      $app->render(500, array("Status" => "Unable to comment on item"));
    }
  } else {
    $app->render(401, array("Status" => "User not logged in"));
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