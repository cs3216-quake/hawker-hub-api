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

  public function findCommentsByItem($itemId) {
    $result = [];
		$db = \Db::getInstance();
    $req = $db->prepare('SELECT * FROM Comment WHERE postId = :postId');
    // the query was prepared, now we replace :id with our actual $id value
		$req->execute(array(
			'postId' => $itemId
			));

      // we create a result of Like objects from the database results
		foreach($req->fetchAll() as $comment) {
      $result[] = new CommentModel($comment['commentId'], $comment['commentDate'], $comment['userId'], $comment['postId'], $comment['message']);
		}

		return $result;
  }

  public function addCommentByItem($itemId, $userId, $message) {
    $db = \Db::getInstance();
    $req = $db->prepare('INSERT INTO Comment (userId, postId, message) VALUES (:userId, :postId, :message)');
    // the query was prepared, now we replace :id with our actual $id value
		$success = $req->execute(array(
      'userId' => $userId,
			'postId' => $itemId,
      'message' => $message
			));

      return $success;
  }
}

?>
