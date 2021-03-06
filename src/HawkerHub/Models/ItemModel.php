<?php

namespace HawkerHub\Models;

use \HawkerHub\Models\UserModel;
use \HawkerHub\Models\LikeModel;
use \HawkerHub\Models\CommentModel;

require_once('DatabaseConnection.php');

class ItemModel extends \HawkerHub\Models\Model{

	public $itemId;
	public $addedDate;
	public $itemName;
	public $photoURL;
	public $caption;
	public $longtitude;
	public $latitude;
	public $user;
	public $comments;
	public $likes;

	public function __construct($itemId,$addedDate,$itemName,$photoURL,$caption,$longtitude,$latitude,$user,$comments, $likes) {
		$this->itemId = $itemId;
		$this->addedDate = $addedDate;
		$this->itemName = html_entity_decode($itemName, ENT_QUOTES, 'UTF-8');
		$this->photoURL = $photoURL;
		$this->caption = html_entity_decode($caption, ENT_QUOTES, 'UTF-8');
		$this->longtitude = $longtitude;
		$this->latitude = $latitude;
		$this->user = $user;
		$this->comments = $comments;
		$this->likes = $likes;
	}

	public static function createNewItem($itemName, $photoURL, $caption, $longtitude=0, $latitude=0, $userId) {
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
				return ItemModel::findByItemId($id,$userId,array());
			}
			return $success;
		} catch (\PDOException $e) {
			return false;
		}
	}

	public static function deleteItem($itemId, $userId) {
		try {
			$db = \Db::getInstance();

      		$itemId = intval($itemId);
     		$userId = intval($userId);

			$req = $db->prepare('DELETE FROM Item where itemId = :itemId and userId = :userId;');

			$success = $req->execute(array(
				'itemId' => $itemId,
				'userId' => $userId
				));
			return $req->rowCount();
		} catch (\PDOException $e) {
			return false;
		}
	}

	public static function listFoodItemSortedByLocation($startAt,$endAt,$latitude,$longtitude,$distance, $ownUserId, $facebookFriendsId) {
		$list = [];
		$db = \Db::getInstance();

		$startAt = intval($startAt);
		$endAt = intval($endAt);
		$ownUserId = intval($ownUserId);
		$facebookFriendsIdImploded = implode(",",$facebookFriendsId);

		$req = $db->prepare('SELECT *, ( 6371 * acos( cos( radians(:lat) ) * cos( radians( latitude ) ) * cos( radians( longtitude ) - radians(:long) ) + sin( radians(:lat) ) * sin( radians( latitude ) ) ) ) AS distance FROM Item,User WHERE Item.UserId = User.UserId and (User.publicProfile = 1 OR User.UserId = :ownUserId OR User.providerUserId IN (:facebookFriendsId)) HAVING distance < :distance ORDER BY distance LIMIT :startAt, :endAt;');

		$req->bindParam(':lat', $latitude, \PDO::PARAM_STR);
		$req->bindParam(':long', $longtitude, \PDO::PARAM_STR);
		$req->bindParam(':distance', $distance, \PDO::PARAM_INT);
		$req->bindParam(':startAt', $startAt, \PDO::PARAM_INT);
		$req->bindParam(':endAt', $endAt, \PDO::PARAM_INT);
		$req->bindParam(':ownUserId', $ownUserId, \PDO::PARAM_INT);
		$req->bindParam(':facebookFriendsId', $facebookFriendsIdImploded, \PDO::PARAM_STR);

		$req->execute();
		foreach($req->fetchAll() as $item) {
			$userId = $item['userId'];
			$itemId = $item['itemId'];

			$user = UserModel::findByUserId($userId);
			$comments = CommentModel::findCommentsByItem($itemId, $ownUserId, $facebookFriendsId);
			$likes = LikeModel::findLikesByItem($itemId, $ownUserId, $facebookFriendsId);

			$list[] = new ItemModel($item['itemId'],$item['addedDate'],$item['itemName'],$item['photoURL'],$item['caption'],$item['longtitude'],$item['latitude'],$user,$comments,$likes);
		}
		return $list;
	}

	public static function listFoodItemSortedByMostRecent($startAt,$endAt, $ownUserId, $facebookFriendsId) {
		$list = [];
		$db = \Db::getInstance();

		$startAt = intval($startAt);
		$endAt = intval($endAt);
		$ownUserId = intval($ownUserId);
		$facebookFriendsIdImploded = implode(",",$facebookFriendsId);

		$req = $db->prepare('SELECT * FROM Item,User WHERE Item.UserId = User.UserId and (User.publicProfile = 1 OR User.UserId = :ownUserId OR User.providerUserId IN (:facebookFriendsId)) ORDER BY Item.addedDate DESC LIMIT :startAt, :endAt;');

		$req->bindParam(':startAt', $startAt, \PDO::PARAM_INT);
		$req->bindParam(':endAt', $endAt, \PDO::PARAM_INT);
		$req->bindParam(':ownUserId', $ownUserId, \PDO::PARAM_INT);
		$req->bindParam(':facebookFriendsId', $facebookFriendsIdImploded, \PDO::PARAM_STR);

		$req->execute();
		foreach($req->fetchAll() as $item) {
			$userId = $item['userId'];
			$itemId = $item['itemId'];

			$user = UserModel::findByUserId($userId);
			$comments = CommentModel::findCommentsByItem($itemId, $ownUserId, $facebookFriendsId);
			$likes = LikeModel::findLikesByItem($itemId, $ownUserId, $facebookFriendsId);

			$list[] = new ItemModel($item['itemId'],$item['addedDate'],$item['itemName'],$item['photoURL'],$item['caption'],$item['longtitude'],$item['latitude'],$user,$comments,$likes);
		}
		return $list;
	}

	public static function listFoodItemByKeyword($orderBy, $startAt, $endAt, $keyword, $ownUserId, $facebookFriendsId) {
		$list = [];
		$db = \Db::getInstance();

		$find = '%'.$keyword.'%';
		$startAt = intval($startAt);
		$endAt = intval($endAt);
		$ownUserId = intval($ownUserId);
		$facebookFriendsIdImploded = implode(",",$facebookFriendsId);

		$sort = '';
		switch($orderBy) {
			case 'recent':
			$sort = 'addedDate DESC';
			break;

			case 'id':
			$sort = 'itemId ASC';
			break;

			default:
			$sort = 'itemId ASC';
			break;
		}

		$req = $db->prepare("SELECT * FROM Item,User WHERE Item.UserId = User.UserId and (User.publicProfile = 1 OR User.UserId = :ownUserId OR User.providerUserId IN (:facebookFriendsId)) AND (Item.itemName LIKE :keyword OR Item.caption LIKE :keyword) ORDER BY $sort LIMIT :startAt, :endAt");

		$req->bindParam(':keyword', $find );
		$req->bindParam(':startAt', $startAt, \PDO::PARAM_INT);
		$req->bindParam(':endAt', $endAt, \PDO::PARAM_INT);
		$req->bindParam(':ownUserId', $ownUserId, \PDO::PARAM_INT);
		$req->bindParam(':facebookFriendsId', $facebookFriendsIdImploded, \PDO::PARAM_STR);

		$req->execute();
		foreach($req->fetchAll() as $item) {
			$userId = $item['userId'];
			$itemId = $item['itemId'];

			$user = UserModel::findByUserId($userId);
			$comments = CommentModel::findCommentsByItem($itemId, $ownUserId, $facebookFriendsId);
			$likes = LikeModel::findLikesByItem($itemId, $ownUserId, $facebookFriendsId);

			$list[] = new ItemModel($item['itemId'],$item['addedDate'],$item['itemName'],$item['photoURL'],$item['caption'],$item['longtitude'],$item['latitude'],$user,$comments,$likes);
		}
		return $list;
	}

	public static function getItemsFromUserId($userId, $startAt, $limit, $ownUserId, $facebookFriendsId) {
		$list = [];
		$db = \Db::getInstance();
      	// we make sure $id is an integer
		$userId = intval($userId);
		$startAt = intval($startAt);
		$limit = intval($limit);
		$ownUserId = intval($ownUserId);
		$facebookFriendsIdImploded = implode(",",$facebookFriendsId);
		$req = $db->prepare('SELECT * FROM Item,User WHERE Item.UserId = User.UserId and (User.publicProfile = 1 OR User.UserId = :ownUserId OR User.providerUserId IN (:facebookFriendsId)) AND Item.UserId = :userId order by Item.itemId ASC limit :startAt, :limit');
      	// the query was prepared, now we replace :id with our actual $id value
		$req->bindParam(':userId', $userId, \PDO::PARAM_INT);
		$req->bindParam(':startAt', $startAt, \PDO::PARAM_INT);
		$req->bindParam(':limit', $limit, \PDO::PARAM_INT);
		$req->bindParam(':ownUserId', $ownUserId, \PDO::PARAM_INT);
		$req->bindParam(':facebookFriendsId', $facebookFriendsIdImploded, \PDO::PARAM_STR);
		$req->execute();

		foreach($req->fetchAll() as $item) {
			$userId = $item['userId'];
			$itemId = $item['itemId'];

			$user = UserModel::findByUserId($userId);
			$comments = CommentModel::findCommentsByItem($itemId, $ownUserId, $facebookFriendsId);
			$likes = LikeModel::findLikesByItem($itemId, $ownUserId, $facebookFriendsId);

			$list[] = new ItemModel($item['itemId'],$item['addedDate'],$item['itemName'],$item['photoURL'],$item['caption'],$item['longtitude'],$item['latitude'],$user,$comments,$likes);
		}
		return $list;
	}

	public static function findByItemId($itemId, $ownUserId, $facebookFriendsId) {
		$db = \Db::getInstance();
      	// we make sure $id is an integer
		$itemId = intval($itemId);
		$ownUserId = intval($ownUserId);
		$facebookFriendsIdImploded = implode(",",$facebookFriendsId);
		$req = $db->prepare('SELECT * FROM Item,User WHERE Item.itemId = :itemId and Item.UserId = User.UserId and (User.publicProfile = 1 OR User.UserId = :userId OR User.providerUserId IN (:facebookFriendsId))');
      	
      	// the query was prepared, now we replace :id with our actual $id value
		$req->execute(array(
			'itemId' => $itemId,
			'userId' => $ownUserId,
			'facebookFriendsId' => $facebookFriendsIdImploded
			));

		$item = $req->fetch();
		if (!$item) {
			return false;
		}
		$userId = $item['userId'];
		$itemId = $item['itemId'];

		$user = UserModel::findByUserId($item['userId']);
		$comments = CommentModel::findCommentsByItem($itemId, $ownUserId, $facebookFriendsId);
		$likes = LikeModel::findLikesByItem($itemId, $ownUserId, $facebookFriendsId);

		return new ItemModel($item['itemId'],$item['addedDate'],$item['itemName'],$item['photoURL'],$item['caption'],$item['longtitude'],$item['latitude'],$user,$comments,$likes);
	}
}
