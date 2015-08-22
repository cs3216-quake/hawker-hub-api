<?php

namespace HawkerHub\Models;

require_once('DatabaseConnection.php');

class UserModel extends \HawkerHub\Models\Model{

	public $userId;
	public $displayName;
	public $providerId;
	public $providerUserId;
	public $providerAccessToken;

	public function __construct($userId,$displayName,$providerId,$providerUserId,$providerAccessToken) {
		$this->userId = $userId;
		$this->displayName = $displayName;
		$this->providerId = $providerId;
		$this->providerUserId = $providerUserId;
		$this->providerAccessToken = $providerAccessToken;
	}

	public static function all() {
		$list = [];
		$db = \Db::getInstance();
		$req = $db->query('SELECT * FROM User');

      // we create a list of Post objects from the database results
		foreach($req->fetchAll() as $user) {
			$list[] = new UserModel($user['userId'], $user['displayName'], $user['providerId'], $user['providerUserId'], $user['providerAccessToken']);
		}

		return $list;
	}

	public static function registerNewUser($displayName, $provider, $providerUserId, $providerAccessToken) {
		$list = [];
		$db = \Db::getInstance();
		$req = $db->query('INSERT INTO User (`displayName`, `providerId`, `providerUserId`, `providerAccessToken`) VALUES (:displayName, (SELECT providerId from Provider where providerName = :provider), :providerUserId, $providerAccessToken);');

		$req->execute(array(
			'displayName' => $displayName,
			'provider' => $provider,
			'providerUserId' => $providerUserId,
			'providerAccessToken' => $providerAccessToken
			));
		// we create a list of Post objects from the database results
		foreach($req->fetchAll() as $user) {
			$list[] = new UserModel($user['userId'], $user['displayName'], $user['providerId'], $user['providerUserId'], $user['providerAccessToken']);
		}

		return $list;
	}

	public static function loginByProviderUserIdAndAccessToken($providerUserId,$providerAccessToken) {
		$db = \Db::getInstance();
      	// we make sure $id is an integer
		$req = $db->prepare('SELECT * FROM User WHERE providerUserId = :providerUserId and providerAccessToken = :providerAccessToken');
      	// the query was prepared, now we replace :id with our actual $id value
		$req->execute(array(
			'providerUserId' => $providerUserId,
			'providerAccessToken' => $providerAccessToken
			));
		$user = $req->fetch();
		if (!$user) {
			throw new \PDOException('Login failed.');
		}
		return new UserModel($user['userId'], $user['displayName'], $user['providerId'], $user['providerUserId'], $user['providerAccessToken']);
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
			return NULL;
		}
		return new UserModel($user['userId'], $user['displayName'], $user['providerId'], $user['providerUserId'], $user['providerAccessToken']);
	}
}

?>
