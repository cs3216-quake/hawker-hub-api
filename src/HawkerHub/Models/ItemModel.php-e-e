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

?>
