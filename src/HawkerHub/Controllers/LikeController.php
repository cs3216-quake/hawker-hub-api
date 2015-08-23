<?php

namespace HawkerHub\Controllers;

use \HawkerHub\Models\LikeModel;

/**
 * Class ItemController
 *
 * Controller class for all retrieval and creation of food items`
 * @package HawkerHub
 **/
class LikeController extends \HawkerHub\Controllers\Controller{

  public function __construct() {
	}

  public function listLikes($itemId) {
    $app = \Slim\Slim::getInstance();
		$likeData = LikeModel::findLikesByItem($itemId);
    $result = array();
    if (empty($likeData)) {
      $result['total'] = 0;
      $result['collection'] = [];
      $app->render(200, $result);
    } else {
      $result['total'] = count($likeData);
      $result['collection'] = $likeData;
      $app->render(200, $result);
    }
  }

  public function insertLike($itemId){
    $currUserId = $this->getCurrentUserId();
    if (is_null($currUserId)) {
      $app->render(401, array("Status" => "User not logged in"));
    }
    $app = \Slim\Slim::getInstance();
		$result = LikeModel::addLikeByItem($itemId, $currUserId);
    if($result) {
      $app->render(200, array("Status" => "OK"));
    } else {
      $app->render(500, array("Status" => "Unable to like item"));
    }
  }

  public function getCurrentUserId() {
    if (isset($_SESSION['userId'])) {
      return $_SESSION['userId'];
    } else {
      return null;
    }
  }
}
?>
