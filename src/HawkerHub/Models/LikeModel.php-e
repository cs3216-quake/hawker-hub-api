<?php

namespace HawkerHub\Models;

require_once('DatabaseConnection.php');

class LikeModel extends \HawkerHub\Models\Model{

  public $likeId;
	public $likeDate;
	public $userId;
	public $postId;

  // Default constructor
	public function __construct($likeId, $likeDate, $userId, $postId) {
    $this->likeId = $likeId;
  	$this->likeDate = $likeDate;
  	$this->userId = $userId;
  	$this->postId = $postId;
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
      $result[] = new LikeModel($like['likeId'], $like['likeDate'], $like['userId'], $like['itemId']);
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
