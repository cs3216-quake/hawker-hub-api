<?php

namespace HawkerHub\Controllers;

use \HawkerHub\Models\ItemModel;
use \HawkerHub\Models\UserModel;
use \Facebook\Facebook;
use \Facebook\Exceptions\FacebookResponseException;
use \Facebook\Exceptions\FacebookSDKException;
use \Facebook\SignedRequest;

/**
 * Class RegisterController
 *
 * Controller class for all registration functions
 * @package HawkerHub
 **/
class ItemController extends \HawkerHub\Controllers\Controller {
	private $fb;
	public function __construct() {
		$this->fb = new Facebook([
			'app_id' => FB_APP_ID,
			'app_secret' => FB_SECRET,
			'default_graph_version' => 'v2.4',
			'cookie' => true
			]);
	}

	public function createNewItem($itemName, $photoURL, $caption, $longtitude, $latitude) {
		$app = \Slim\Slim::getInstance();
		$caption = htmlspecialchars($caption, ENT_QUOTES, 'UTF-8');
		$itemName = htmlspecialchars($itemName, ENT_QUOTES, 'UTF-8');
		$userController = new \HawkerHub\Controllers\UserController();

		if ($userController->isLoggedIn()) {
			$success = ItemModel::createNewItem($itemName, $photoURL, $caption, $longtitude, $latitude, $_SESSION['userId']);
			if (!$success) {
				$app->render(500, ['Status' => 'An error occured while adding item.' ]);
			} else {
				$response = $this->fb->POST(
				  'me/objects/hawker-hub:food',
				  array( 'object' =>
				  json_encode(array(
				    'og:url' => 'http://hawkerhub.quanyang.me/item/'.$success->itemId,
				    'og:title' => $success->itemName,
				    'og:type' => 'hawker-hub:food',
				    'og:image' => $success->photoURL,
				    'og:description' => $success->caption,
				    'fb:app_id' => '1466024120391100'
				  ))
				  ),
				  $_SESSION['fb_access_token']
				);
				$app->render(201, (array) $success);
			}
		} else {
			$app->render(401, ['Status' => 'Not logged in.' ]);
		}
	}

	public function deleteItem($itemId) {
		$app = \Slim\Slim::getInstance();
		$userController = new \HawkerHub\Controllers\UserController();

		if ($userController->isLoggedIn()) {
			$success = ItemModel::deleteItem($itemId, $_SESSION['userId']);
			if (!$success) {
				$app->render(500, ['Status' => 'An error occured while deleting item.' ]);
			} else {
				$app->render(204);
			}
		} else {
			$app->render(401, ['Status' => 'Not logged in.' ]);
		}
	}

	public function findByItemId($itemId) {
		$app = \Slim\Slim::getInstance();
		$userController = new \HawkerHub\Controllers\UserController();
		$item = ItemModel::findByItemId($itemId,$_SESSION['userId'],$userController->getAllFacebookFriendsId());
		if ($item) {
			$app->render(200, (array) $item);
		} else {
			$app->render(500, ['Status' => 'Item not found.']);
		}
	}

	public function listFoodItem($orderBy = "id", $startAt = 0, $limit = 15, $latitude, $longtitude, $keyword = '') {
		if (!empty($keyword)) {
			// Search by keyword
			$this->searchFoodItemByKeyword($orderBy, $startAt, $limit, $keyword);
		}else if ($orderBy == 'location' && @$latitude && @$longtitude) {
			//Sort by location
			$this->listFoodItemSortedByLocation($startAt,$limit,$latitude,$longtitude);
		} else {
			//Sort by most recent
			$this->listFoodItemSortedByMostRecent($startAt,$limit);
		}
	}

	private function searchFoodItemByKeyword($orderBy, $startAt, $limit, $keyword) {
		$userController = new \HawkerHub\Controllers\UserController();
		$app = \Slim\Slim::getInstance();
		$item = ItemModel::listFoodItemByKeyword($orderBy, $startAt, $limit, $keyword,$_SESSION['userId'],$userController->getAllFacebookFriendsId());
		if ($item) {
			$app->render(200, $item);
		} else {
			$app->render(500, ['Status' => 'No items found.']);
		}
	}

	private function listFoodItemSortedByLocation($startAt = 0, $limit = 15, $latitude, $longtitude) {
		$app = \Slim\Slim::getInstance();
		$userController = new \HawkerHub\Controllers\UserController();
		$distance = 10; //Kilometers

		$item = ItemModel::listFoodItemSortedByLocation($startAt,$limit,$latitude,$longtitude,$distance,$_SESSION['userId'],$userController->getAllFacebookFriendsId());
		if ($item) {
			$app->render(200, $item);
		} else {
			$app->render(500, ['Status' => 'No items found.']);
		}
	}

	private function listFoodItemSortedByMostRecent($startAt = 0, $limit = 15) {
		$app = \Slim\Slim::getInstance();
		$userController = new \HawkerHub\Controllers\UserController();
		
		$item = ItemModel::listFoodItemSortedByMostRecent($startAt,$limit,$_SESSION['userId'],$userController->getAllFacebookFriendsId());
		if ($item) {
			$app->render(200, $item);
		} else {
			$app->render(500, ['Status' => 'No items found.']);
		}
	}

}
