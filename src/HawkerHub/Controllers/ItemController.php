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

	public function findByItemId($itemId) {
		$app = \Slim\Slim::getInstance();
		$item = ItemModel::findByItemId($itemId);
		if ($item) {
			$app->render(200, ['item' => json_encode($item)]);
		} else {
			$app->render(500, ['Status' => 'Item not found.']);
		}
	}

	public function listFoodItemSortedByLocation($startAt = 0, $limit = 15, $lat, $long) {
		$app = \Slim\Slim::getInstance();
		$distance = 10; //Kilometers

		$item = ItemModel::listFoodItemSortedByLocation($startAt,$limit,$lat,$long,$distance);
		if ($item) {
			$app->render(200, ['item' => json_encode($item)]);
		} else {
			$app->render(500, ['Status' => 'Item not found.']);
		}
	}

	public function listFoodItemSortedByMostRecent($startAt = 0, $limit = 15) {
		$app = \Slim\Slim::getInstance();
		
		$item = ItemModel::listFoodItemSortedByMostRecent($startAt,$limit);
		if ($item) {
			$app->render(200, ['item' => json_encode($item)]);
		} else {
			$app->render(500, ['Status' => 'No items found.']);
		}
	}

}

