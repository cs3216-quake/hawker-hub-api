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

  public function findLikesByItem($itemId) {
    $result = [];
		$db = \Db::getInstance();
    $req = $db->prepare('SELECT * FROM Approve WHERE postId = :postId');
    // the query was prepared, now we replace :id with our actual $id value
		$req->execute(array(
			'postId' => $itemId
			));

      // we create a result of Like objects from the database results
		foreach($req->fetchAll() as $like) {
      $result[] = new LikeModel($like['likeId'], $like['likeDate'], $like['userId'], $like['postId']);
		}

		return $result;
  }

  public function addLikeByItem($itemId, $userId) {
    $db = \Db::getInstance();
    $req = $db->prepare('INSERT INTO Approve (userId, postId) VALUES (:userId, :postId)');
    // the query was prepared, now we replace :id with our actual $id value
		$success = $req->execute(array(
      'userId' => $userId,
			'postId' => $itemId
			));

      return $success;
  }
}

?>