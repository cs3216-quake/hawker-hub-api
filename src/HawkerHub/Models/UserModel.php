<?php

namespace HawkerHub\Models;

use \HawkerHub\Models\UserModel;

require_once('DatabaseConnection.php');

class UserModel extends \HawkerHub\Models\Model{

	public $userId;
	public $displayName;
	public $providerId;
	public $providerUserId;

	public function __construct($userId,$displayName,$providerId,$providerUserId) {
		$this->userId = $userId;
		$this->displayName = $displayName;
		$this->providerId = $providerId;
		$this->providerUserId = $providerUserId;
	}

	public static function getUserCount() {
		$db = \Db::getInstance();
		$req = $db->query('SELECT COUNT(*) FROM User');
		$req->execute();
		$count = $req->fetch()[0];
		return $count;
	}

	public static function all() {
		$list = [];
		$db = \Db::getInstance();
		$req = $db->query('SELECT * FROM User');

      // we create a list of Post objects from the database results
		foreach($req->fetchAll() as $user) {
			$list[] = new UserModel($user['userId'], $user['displayName'], $user['providerId'], $user['providerUserId']);
		}

		return $list;
	}

	public static function updateSettings($userId, $privacy) {
		try {
	      $db = \Db::getInstance();
	      $userId = intval($userId);
	      $privacy = intval($privacy);

	      $req = $db->prepare('UPDATE User SET publicProfile = :privacy where userId = :userId;');

	      $success = $req->execute(array(
	        'userId' => $userId,
	        'privacy' => $privacy
	        ));

	      return $success;
	    } catch (\PDOException $e) {
	      return false;
	    }
	}

	public static function deleteUserWithProviderUserId($providerUserId) {
	    try {
	      $db = \Db::getInstance();
	      $providerUserId = intval($providerUserId);

	      $req = $db->prepare('DELETE FROM User where providerUserId = :providerUserId;');

	      $success = $req->execute(array(
	        'providerUserId' => $providerUserId
	        ));
	      return $req->rowCount();
	    } catch (\PDOException $e) {
	      return false;
	    }
	}

	public static function registerNewUser($displayName, $provider, $providerUserId) {
		try {
		$db = \Db::getInstance();
		$req = $db->prepare('INSERT INTO User (`displayName`, `providerId`, `providerUserId`) VALUES (:displayName, (SELECT providerId from Provider where providerName = :provider), :providerUserId);');

		$success = $req->execute(array(
			'displayName' => $displayName,
			'provider' => $provider,
			'providerUserId' => $providerUserId
			));

		return $success;
		} catch (\PDOException $e) {
			return false;
		}
	}

	//	Helper method to call ItemModel function.
	public static function getItemsFromUserId($userId, $startAt, $limit, $ownUserId, $facebookFriendsId) {
		return ItemModel::getItemsFromUserId($userId, $startAt, $limit, $ownUserId, $facebookFriendsId);
	}

	public static function canViewItem($itemId, $ownUserId, $facebookFriendsId) {
		$db = \Db::getInstance();

		$itemId = intval($itemId);
		$ownUserId = intval($ownUserId);
		$facebookFriendsId = implode(",",$facebookFriendsId);

		$req = $db->prepare('SELECT * FROM User,Item WHERE Item.userId = User.userId and  Item.itemId = :itemId and (User.publicProfile = 1 OR User.UserId = :ownUserId OR User.providerUserId IN (:facebookFriendsId)) ');
      	// the query was prepared, now we replace :id with our actual $id value
		$req->execute(array(
			'itemId' => $itemId,
			'ownUserId' => $ownUserId,
			'facebookFriendsId' => $facebookFriendsId
			));
		$success = $req->fetch();
		if (!$success) {
			return false;
		}
		return true;
	}

	public static function canViewItemsByUserId($userId, $ownUserId, $facebookFriendsId) {
		$db = \Db::getInstance();

		$userId = intval($userId);
		$ownUserId = intval($ownUserId);
		$facebookFriendsId = implode(",",$facebookFriendsId);

		$req = $db->prepare('SELECT * FROM User WHERE userId = :userId and (User.publicProfile = 1 OR User.UserId = :ownUserId OR User.providerUserId IN (:facebookFriendsId)) ');
      	// the query was prepared, now we replace :id with our actual $id value
		$req->execute(array(
			'userId' => $userId,
			'ownUserId' => $ownUserId,
			'facebookFriendsId' => $facebookFriendsId
			));
		$success = $req->fetch();
		if (!$success) {
			return false;
		}
		return true;
	}

	public static function findByProviderUserId($userId) {
		$db = \Db::getInstance();

		$req = $db->prepare('SELECT * FROM User WHERE providerUserId = :userId');
      	// the query was prepared, now we replace :id with our actual $id value
		$req->execute(array('userId' => $userId));
		$user = $req->fetch();
		if (!$user) {
			return false;
		}
		return new UserModel($user['userId'], $user['displayName'], $user['providerId'], $user['providerUserId']);
	}

	public static function findByUserId($userId) {
		$db = \Db::getInstance();
      	// we make sure $id is an integer
		$userId = intval($userId);
		$req = $db->prepare('SELECT * FROM User WHERE UserId = :userId');
      	// the query was prepared, now we replace :id with our actual $id value
		$req->execute(array('userId' => $userId));
		$user = $req->fetch();
		if (!$user) {
			return false;
		}
		return new UserModel($user['userId'], $user['displayName'], $user['providerId'], $user['providerUserId']);
	}
}
