<?php

namespace HawkerHub\Controllers;

use \HawkerHub\Models\UserModel;

/**
 * Class RegisterController
 *
 * Controller class for all registration functions
 * @package HawkerHub
 **/
class UserController extends \HawkerHub\Controllers\Controller{

	public function __construct($model) {
		parent::__construct($model);
	}

	public function register($displayName, $provider, $providerUserId, $providerAccessToken) {
		

		
	}

	public function login($providerUserId,$providerAccessToken) {
		$app = \Slim\Slim::getInstance();
		try {
			$user = UserModel::loginByProviderUserIdAndAccessToken($providerUserId,$providerAccessToken);
			$app->render(200, ['user' => json_encode($user)]);
		} catch(PDOException $e) {
			$this->app->render(500, ['Status' => '{"error":{"text":'. $e->getMessage() .'}}']);
		}
	}


}
?>
