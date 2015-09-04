<?php

namespace HawkerHub\Controllers;

use \HawkerHub\Models\ItemModel;
use \HawkerHub\Models\UserModel;
use \Facebook\Facebook;
use \Facebook\Exceptions\FacebookResponseException;

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
			'cookie' => true
			]);
	}

	public function createNewItem($itemName, $photoURL, $caption, $longtitude=0, $latitude=0, $shareToFacebook) {
		$app = \Slim\Slim::getInstance();

		if ( !is_int(intval($longtitude)) ||!is_int(intval($latitude)) || empty(trim($caption)) || is_null($caption) || strlen($caption) > 255 || empty(trim($itemName)) || is_null($itemName) || strlen($itemName) > 35 ) {
			$app->render(400, ['Status' => 'input is invalid.' ]);
			return;
		}

		$caption = trim(htmlspecialchars($caption, ENT_QUOTES, 'UTF-8'));
		$itemName = trim(htmlspecialchars($itemName, ENT_QUOTES, 'UTF-8'));
		$userController = new \HawkerHub\Controllers\UserController();

		$longtitude= $longtitude===null?0:$longtitude;
		$latitude= $latitude===null?0:$latitude;
		if ($userController->isLoggedIn()) {
			$success = ItemModel::createNewItem($itemName, $photoURL, $caption, $longtitude, $latitude, $_SESSION['userId']);
			if (!$success) {
				$app->render(500, ['Status' => 'An error occured while adding item.' ]);
			} else {
				try {

					if ($shareToFacebook) {
						$response = $this->fb->POST(
							'me/objects/hawker-hub:food',
							array( 'object' =>
								json_encode(array(
									'og:url' => 'http://hawkerhub.quanyang.me/food/'.$success->itemId,
									'og:title' => $success->itemName,
									'og:type' => 'hawker-hub:food',
									'og:image' => $success->photoURL,
									'og:description' => $success->caption,
									'fb:app_id' => '1466024120391100'
									))
								),
							$_SESSION['fb_access_token']
							);
					}
				} catch (FacebookResponseException $e) {

				}
				$app->render(201, (array) $success);
			}
		} else {
			$app->render(401, ['Status' => 'Not logged in.' ]);
		}
	}

	public function deleteItem($itemId) {
		$app = \Slim\Slim::getInstance();

		if (!is_int(intval($itemId))) {
			$app->render(400, ['Status' => 'input is invalid.' ]);
			return;
		}

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

		if (!is_int(intval($itemId))) {
			$app->render(400, ['Status' => 'input is invalid.' ]);
			return;
		}

		$userController = new \HawkerHub\Controllers\UserController();
		if($userController->isLoggedIn()) {
			$item = ItemModel::findByItemId($itemId,$_SESSION['userId'],$userController->getAllFacebookFriendsId());
			if ($item) {
				$app->render(200, (array) $item);
			} else {
				$app->render(200, []);
			}
		} else {
			$app->render(401, array("Status" => "User not logged in"));
		}
	}

	public function listFoodItem($orderBy = "id", $startAt = 0, $limit = 15, $latitude, $longtitude, $keyword = '') {
		$userController = new \HawkerHub\Controllers\UserController();

		if (!is_int(intval($longtitude)) ||!is_int(intval($latitude)) || !is_int(intval($startAt)) || !is_int(intval($limit))) {
			$app->render(400, ['Status' => 'input is invalid.' ]);
			return;
		}

		if($userController->isLoggedIn()) {
			if (!empty(trim($keyword))) {
				// Search by keyword
				$this->searchFoodItemByKeyword($orderBy, $startAt, $limit, $keyword);
			}else if (($orderBy == 'location' && @$latitude && @$longtitude)) {
				//Sort by location
				$this->listFoodItemSortedByLocation($startAt,$limit,$latitude,$longtitude);
			} else {
				//Sort by most recent
				$this->listFoodItemSortedByMostRecent($startAt,$limit);
			}
		} else {
			$app = \Slim\Slim::getInstance();
			$app->render(401, array("Status" => "User not logged in"));
		}
	}

	private function searchFoodItemByKeyword($orderBy, $startAt, $limit, $keyword) {
		$userController = new \HawkerHub\Controllers\UserController();
		$app = \Slim\Slim::getInstance();
		$item = ItemModel::listFoodItemByKeyword($orderBy, $startAt, $limit, $keyword,$_SESSION['userId'],$userController->getAllFacebookFriendsId());
		if ($item) {
			$app->render(200, $item);
		} else {
			$app->render(200, []);
		}
	}

	private function listFoodItemSortedByLocation($startAt = 0, $limit = 15, $latitude, $longtitude) {
		$app = \Slim\Slim::getInstance();
		$userController = new \HawkerHub\Controllers\UserController();
		$distance = 5; //Kilometers

		$item = ItemModel::listFoodItemSortedByLocation($startAt,$limit,$latitude,$longtitude,$distance,$_SESSION['userId'],$userController->getAllFacebookFriendsId());
		if ($item) {
			$app->render(200, $item);
		} else {
			$app->render(200, []);
		}
	}

	private function listFoodItemSortedByMostRecent($startAt = 0, $limit = 15) {
		$app = \Slim\Slim::getInstance();
		$userController = new \HawkerHub\Controllers\UserController();
		
		$item = ItemModel::listFoodItemSortedByMostRecent($startAt,$limit,$_SESSION['userId'],$userController->getAllFacebookFriendsId());
		if ($item) {
			$app->render(200, $item);
		} else {
			$app->render(200, []);
		}
	}

}
