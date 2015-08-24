<?php

namespace HawkerHub\Models;

use \HawkerHub\Models\UserModel;

require_once('DatabaseConnection.php');

class LikeModel extends \HawkerHub\Models\Model {

	public $likeDate;
	public $userId;
  public $user;

  // Default constructor
	public function __construct($likeDate, $userId, $user) {
  	$this->likeDate = $likeDate;
  	$this->userId = $userId;
  	$this->user = $user;
	}

  public static function findLikesByItem($itemId) {
    $result = [];
		$db = \Db::getInstance();
    $itemId = intval($itemId);
    $req = $db->prepare('SELECT * FROM Approve WHERE itemId = :itemId');
    // the query was prepared, now we replace :id with our actual $id value
		$req->execute(array(
			'itemId' => $itemId
			));

      // we create a result of Like objects from the database results
		foreach($req->fetchAll() as $like) {
      $userId = $like['userId'];

      $user = UserModel::findByUserId($userId);
      $result[] = new LikeModel($like['likeDate'], $like['userId'], $user);
		}

		return $result;
  }

  public static function addLikeByItem($itemId, $userId) {
    $db = \Db::getInstance();
    $itemId = intval($itemId);
    $userId = intval($userId);
    $req = $db->prepare('INSERT INTO Approve (userId, itemId) VALUES (:userId, :itemId)');
    // the query was prepared, now we replace :id with our actual $id value
		$success = $req->execute(array(
      'userId' => $userId,
			'itemId' => $itemId
			));

      return $success;
  }
}
