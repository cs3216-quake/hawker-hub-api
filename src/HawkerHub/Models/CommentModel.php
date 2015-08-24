<?php

namespace HawkerHub\Models;

require_once('DatabaseConnection.php');

class CommentModel extends \HawkerHub\Models\Model{

  public $commentId;
  public $commentDate;
  public $userId;
  public $postId;
  public $message;

  // Default constructor
  public function __construct($commentId, $commentDate, $userId, $postId, $message) {
    $this->commentId = $commentId;
    $this->commentDate = $commentDate;
    $this->userId = $userId;
    $this->postId = $postId;
    $this->message = $message;
  }

  public static function findCommentsByItem($itemId) {
    $result = [];
    $db = \Db::getInstance();
    $itemId = intval($itemId);
    $req = $db->prepare('SELECT * FROM Comment WHERE itemId = :itemId');
    // the query was prepared, now we replace :id with our actual $id value
    $req->execute(array(
     'itemId' => $itemId
    ));    

      // we create a result of Like objects from the database results
    foreach($req->fetchAll() as $comment) {
      $result[] = new CommentModel($comment['commentId'], $comment['commentDate'], $comment['userId'], $comment['itemId'], $comment['message']);
    }

    return $result;
  }

  public static function addCommentByItem($itemId, $userId, $message) {
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
  }
}