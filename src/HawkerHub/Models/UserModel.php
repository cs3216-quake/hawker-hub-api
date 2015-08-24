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

	public static function getItemsFromUserId($userId, $startAt, $limit) {
		$list = [];
		$db = \Db::getInstance();
      	// we make sure $id is an integer
		$userId = intval($userId);
		$startAt = intval($startAt);
		$limit = intval($limit);
		$req = $db->prepare('SELECT * FROM Item WHERE UserId = :userId order by itemId ASC limit :startAt, :limit');
      	// the query was prepared, now we replace :id with our actual $id value
		$req->bindParam(':userId', $userId, \PDO::PARAM_INT);
		$req->bindParam(':startAt', $startAt, \PDO::PARAM_INT);
		$req->bindParam(':limit', $limit, \PDO::PARAM_INT);
		$req->execute();

		foreach($req->fetchAll() as $item) {
			$list[] = new ItemModel($item['itemId'],$item['addedDate'],$item['itemName'],$item['photoURL'],$item['caption'],$item['longtitude'],$item['latitude'],$item['userId']);
		}
		return $list;
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
