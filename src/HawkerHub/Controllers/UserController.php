<?php

namespace HawkerHub\Controllers;

use \HawkerHub\Models\UserModel;

/**
 * Class RegisterController
 *
 * Controller class for all registration functions
 * @package HawkerHub
 **/
class UserController extends \HawkerHub\Controllers\Controller {

	public function __construct($model) {
		parent::__construct($model);
	}

	public function register($displayName, $provider, $providerUserId, $providerAccessToken) {
		$app = \Slim\Slim::getInstance();
		$success = UserModel::registerNewUser($displayName, $provider, $providerUserId, $providerAccessToken);
		if (!$success) { 
			$app->render(500, ['Status' => 'Registration failed.' ]);
		} else {
			$app->render(200, ['Status' => 'Registration successful.' ]);
		}
	}

	public function login($providerUserId,$providerAccessToken) {
		$app = \Slim\Slim::getInstance();
		$user = UserModel::loginByProviderUserIdAndAccessToken($providerUserId,$providerAccessToken);
		if ($user) {
			$app->render(200, ['user' => json_encode($user)]);
		} else {
			$app->render(500, ['Status' => 'Login failed.']);
		}
	}


}

