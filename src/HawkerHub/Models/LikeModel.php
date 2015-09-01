<?php

namespace HawkerHub\Models;

use \HawkerHub\Models\UserModel;

require_once('DatabaseConnection.php');

class LikeModel extends \HawkerHub\Models\Model {

	public $likeDate;
	public $user;

  // Default constructor
	public function __construct($likeDate, $user) {
  	$this->likeDate = $likeDate;
  	$this->user = $user;
	}

  public static function deleteLike($itemId, $userId) {
    try {
      $db = \Db::getInstance();

      $itemId = intval($itemId);
      $userId = intval($userId);
      
      $req = $db->prepare('DELETE FROM Approve where itemId = :itemId and userId = :userId;');

      $success = $req->execute(array(
        'itemId' => $itemId,
        'userId' => $userId
        ));
      return $req->rowCount();
    } catch (\PDOException $e) {
      return false;
    }
  }

  public static function findLikesByItem($itemId, $ownUserId, $facebookFriendsId) {
    $result = [];
    $db = \Db::getInstance();
    $itemId = intval($itemId);
    $ownUserId = intval($ownUserId);
    $facebookFriendsId = implode(",",$facebookFriendsId);

    $req = $db->prepare('SELECT * FROM Approve,Item,User WHERE Approve.itemId = Item.itemId and Approve.userId = User.userId and (User.publicProfile = 1 OR User.UserId = :ownUserId OR User.providerUserId IN (:facebookFriendsId)) and Item.itemId = :itemId');
    // the query was prepared, now we replace :id with our actual $id value
		$req->execute(array(
			'itemId' => $itemId,
      'ownUserId' => $ownUserId,
      'facebookFriendsId' => $facebookFriendsId
			));

      // we create a result of Like objects from the database results
		foreach($req->fetchAll() as $like) {
			$userId = $like['userId'];

			$user = UserModel::findByUserId($userId);
			$result[] = new LikeModel($like['likeDate'], $user);
		}

		return $result;
  }

  public static function addLikeByItemId($itemId, $userId) {
    try {
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
    } catch (\PDOException $e) {
      return false;
    }
  }
}
