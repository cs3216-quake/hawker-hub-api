<?php

namespace HawkerHub\Models;

use \HawkerHub\Models\UserModel;

require_once('DatabaseConnection.php');

class CommentModel extends \HawkerHub\Models\Model{

  public $commentId;
  public $commentDate;
  public $user;
  public $message;

  // Default constructor
  public function __construct($commentId, $commentDate, $user, $message) {
    $this->commentId = $commentId;
    $this->commentDate = $commentDate;
    $this->user = $user;
    $this->message = $message;
  }

  public static function deleteComment($itemId, $commentId, $userId) {
    try {
      $db = \Db::getInstance();
      $itemId = intval($itemId);
      $commentId = intval($commentId);
      $userId = intval($userId);

      $req = $db->prepare('DELETE FROM Comment where itemId = :itemId and userId = :userId and commentId = :commentId;');

      $success = $req->execute(array(
        'itemId' => $itemId,
        'userId' => $userId,
        'commentId' => $commentId
        ));
      return $req->rowCount();
    } catch (\PDOException $e) {
      return false;
    }
  }

  public static function findCommentsByItem($itemId, $ownUserId, $facebookFriendsId) {
    $result = [];
    $db = \Db::getInstance();
    $itemId = intval($itemId);
    $ownUserId = intval($ownUserId);
    $facebookFriendsId = implode(",",$facebookFriendsId);

    $req = $db->prepare('SELECT * FROM Comment,Item,User WHERE Comment.userId = User.userId and Comment.itemId = Item.itemId and (User.publicProfile = 1 OR User.UserId = :ownUserId OR User.providerUserId IN (:facebookFriendsId)) and Comment.itemId = :itemId');
    // the query was prepared, now we replace :id with our actual $id value
    $req->execute(array(
      'itemId' => $itemId,
      'ownUserId' => $ownUserId,
      'facebookFriendsId' => $facebookFriendsId
    ));

      // we create a result of Like objects from the database results
    foreach($req->fetchAll() as $comment) {
        $userId = $comment['userId'];

        $user = UserModel::findByUserId($userId);
        $result[] = new CommentModel($comment['commentId'], $comment['commentDate'], $user, $comment['message']);
    }

    return $result;
  }

  public static function addCommentByItem($itemId, $userId, $message) {
    try {
      $db = \Db::getInstance();
      $itemId = intval($itemId);
      $userId = intval($userId);
      $req = $db->prepare('INSERT INTO Comment (userId, itemId, message) VALUES (:userId, :itemId, :message)');
      // the query was prepared, now we replace :id with our actual $id value
      $success = $req->execute(array(
        'userId' => $userId,
        'itemId' => $itemId,
        'message' => $message
        ));

      return $success;
    } catch (\PDOException $e) {
      return false;
    }
  }
}
