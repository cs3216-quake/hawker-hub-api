<?php

namespace HawkerHub\Controllers;

use \HawkerHub\Models\ItemModel;

/**
 * Class RegisterController
 *
 * Controller class for all registration functions
 * @package HawkerHub
 **/
class ItemController extends \HawkerHub\Controllers\Controller {

	public function __construct() {
	}

	public function createNewItem($itemName, $photoURL, $caption, $longtitude, $latitude) {
		$app = \Slim\Slim::getInstance();
		$success = ItemModel::createNewItem($itemName, $photoURL, $caption, $longtitude, $latitude, 1);
		if (!$success) { 
			$app->render(500, ['Status' => 'An error occured while adding item.' ]);
		} else {
			$app->render(201, ['item' => json_encode($success)]);
		}
	}

	public function findByItemId($itemId) {
		$app = \Slim\Slim::getInstance();
		$item = ItemModel::findByItemId($itemId);
		if ($item) {
			$app->render(200, ['item' => json_encode($item)]);
		} else {
			$app->render(500, ['Status' => 'Item not found.']);
		}
	}

	public function listFoodItemSortedByLocation($startAt = 0, $limit = 15, $latitude, $longtitude) {
		$app = \Slim\Slim::getInstance();
		$distance = 10; //Kilometers

		$item = ItemModel::listFoodItemSortedByLocation($startAt,$limit,$latitude,$longtitude,$distance);
		if ($item) {
			$app->render(200, [
				'startAt' => $startAt,
				'limit' => $limit,
				'orderBy' => 'location',
				'latitude' => $latitude,
				'longtitude' => $longtitude,
				'collection' => json_encode($item)
				]);
		} else {
			$app->render(500, ['Status' => 'Item not found.']);
		}
	}

	public function listFoodItemSortedByMostRecent($startAt = 0, $limit = 15) {
		$app = \Slim\Slim::getInstance();
		
		$item = ItemModel::listFoodItemSortedByMostRecent($startAt,$limit);
		if ($item) {
			$app->render(200, [
				'startAt' => $startAt,
				'limit' => $limit,
				'orderBy' => 'datetime',
				'collection' => json_encode($item)
				]);
		} else {
			$app->render(500, ['Status' => 'No items found.']);
		}
	}

}

