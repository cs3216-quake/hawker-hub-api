<?php

namespace HawkerHub\Models;

require_once('DatabaseConnection.php');

class ItemModel extends \HawkerHub\Models\Model{

	public $itemId;
	public $addedDate;
	public $itemName;
	public $photoURL;
	public $caption;
	public $longtitude;
	public $latitude;
	public $userId;

	public function __construct($itemId,$addedDate,$itemName,$photoURL,$caption,$longtitude,$latitude,$userId) {
		$this->itemId = $itemId;
		$this->addedDate = $addedDate;
		$this->itemName = $itemName;
		$this->photoURL = $photoURL;
		$this->caption = $caption;
		$this->longtitude = $longtitude;
		$this->latitude = $latitude;
		$this->userId = $userId;
	}

	public static function all() {
		$list = [];
		$db = \Db::getInstance();
		$req = $db->query('SELECT * FROM Item');

      // we create a list of Post objects from the database results
		foreach($req->fetchAll() as $item) {
			$list[] = new ItemModel($item['itemId'],$item['addedDate'],$item['itemName'],$item['photoURL'],$item['caption'],$item['longtitude'],$item['latitude'],$item['userId']);
		}

		return $list;
	}

	public static function createNewItem($itemName, $photoURL, $caption, $longtitude, $latitude,$userId) {
		try {
		$db = \Db::getInstance();
		$req = $db->prepare('INSERT INTO Item (`itemName`, `photoURL`, `caption`, `longtitude`, `latitude`, `userId`) VALUES (:itemName, :photoURL, :caption, :longtitude, :latitude, :userId);');

		$success = $req->execute(array(
			'itemName' => $itemName,
			'photoURL' => $photoURL,
			'caption' => $caption,
			'longtitude' => $longtitude,
			'latitude' => $latitude,
			'userId' => $userId
			));

		$id = $db->lastInsertId();

		if ($id > 0 && $success) {
			return ItemModel::findByItemId($id);
		}

		return $success;
		} catch (\PDOException $e) {
			print $e;
			return false;
		}
	}

	public static function listFoodItemSortedByLocation($startAt,$endAt,$latitude,$longtitude,$distance) {
		$list = [];
		$db = \Db::getInstance();

		$startAt = intval($startAt);
		$endAt = intval($endAt);

		$req = $db->prepare('SELECT *, ( 6371 * acos( cos( radians(:lat) ) * cos( radians( latitude ) ) * cos( radians( longtitude ) - radians(:long) ) + sin( radians(:lat) ) * sin( radians( latitude ) ) ) ) AS distance FROM Item HAVING distance < :distance ORDER BY distance LIMIT :startAt, :endAt;');

		$req->bindParam(':lat', $latitude, \PDO::PARAM_STR);
		$req->bindParam(':long', $longtitude, \PDO::PARAM_STR);
		$req->bindParam(':distance', $distance, \PDO::PARAM_INT);
		$req->bindParam(':startAt', $startAt, \PDO::PARAM_INT);
		$req->bindParam(':endAt', $endAt, \PDO::PARAM_INT);

		$req->execute();
		foreach($req->fetchAll() as $item) {
			$list[] = new ItemModel($item['itemId'],$item['addedDate'],$item['itemName'],$item['photoURL'],$item['caption'],$item['longtitude'],$item['latitude'],$item['userId']);
		}
		return $list;
	}

	public static function listFoodItemSortedByMostRecent($startAt,$endAt) {
		$list = [];
		$db = \Db::getInstance();

		$startAt = intval($startAt);
		$endAt = intval($endAt);

		$req = $db->prepare('SELECT * FROM Item ORDER BY addedDate DESC LIMIT :startAt, :endAt;');

		$req->bindParam(':startAt', $startAt, \PDO::PARAM_INT);
		$req->bindParam(':endAt', $endAt, \PDO::PARAM_INT);

		$req->execute();
		foreach($req->fetchAll() as $item) {
			$list[] = new ItemModel($item['itemId'],$item['addedDate'],$item['itemName'],$item['photoURL'],$item['caption'],$item['longtitude'],$item['latitude'],$item['userId']);
		}
		return $list;
	}

	public static function findByItemId($itemId) {
		$db = \Db::getInstance();
      	// we make sure $id is an integer
		$userId = intval($itemId);
		$req = $db->prepare('SELECT * FROM Item WHERE itemId = :itemId');
      	// the query was prepared, now we replace :id with our actual $id value
		$req->execute(array('itemId' => $itemId));
		$item = $req->fetch();
		if (!$item) {
			return false;
		}
		return new ItemModel($item['itemId'],$item['addedDate'],$item['itemName'],$item['photoURL'],$item['caption'],$item['longtitude'],$item['latitude'],$item['userId']);
	}
}