<?php

namespace HawkerHub\Controllers;

use \HawkerHub\Models\UserModel;
use \Facebook\Facebook;

/**
 * Class RegisterController
 *
 * Controller class for all registration functions
 * @package HawkerHub
 **/
class UserController extends \HawkerHub\Controllers\Controller {
	public $fb;
	public function __construct() {
		$this->fb = new Facebook([
			'app_id' => '1466024120391100',
			'app_secret' => '',
			'default_graph_version' => 'v2.2',
			]);
	}

	public function register($displayName, $provider, $providerUserId) {
		$app = \Slim\Slim::getInstance();
		$success = UserModel::registerNewUser($displayName, $provider, $providerUserId);
		if (!$success) {
			$app->render(500, ['Status' => 'Registration failed.' ]);
		} else {
			$app->render(200, ['Status' => 'Registration successful.' ]);
		}
	}

	public function logout() {
		unsetLoginVariables();
		$app->render(200, ['Status' => 'Logout successful.' ]);
	}

	public function unsetLoginVariables() {
		unset($_SESSION['fb_access_token']);
		unset($_SESSION['userId']);
	}

	public function isLoggedIn() {
		return @$_SESSION['userId'];
	}

	public function checkFacebookAccessTokenValidity() {
		$this->fb->setAccessToken($_SESSION['fb_access_token']);

		if (($userId = $fb->getUser())) {
		} else {
			unsetLoginVariables();
		}
	}

	public function login() {
		$app = \Slim\Slim::getInstance();
		// verify with Facebook using the Facebook PHP SDK

		print "A";
		$this->checkFacebookAccessTokenValidity();
		if (!@$_SESSION['fb_access_token']) {
			print "B";
			$helper = $fb->getJavaScriptHelper();

			try {
				$accessToken = $helper->getAccessToken();
			} catch(Facebook\Exceptions\FacebookResponseException $e) {
  			// When Graph returns an error
				$app->render(500, ['Status' => 'Login failed. '. $e->getMessage()  ]);
				return;
			} catch(Facebook\Exceptions\FacebookSDKException $e) {
 			 // When validation fails or other local issues
				$app->render(500, ['Status' => 'Login failed. '. $e->getMessage() ]);
				return;
			}
			if (! isset($accessToken)) {
				$app->render(500, ['Status' => 'Login failed.' ]);
				return;
			}

			$_SESSION['fb_access_token'] = (string) $accessToken;
		}

		print "B";

		try {
		  // Returns a `Facebook\FacebookResponse` object
			$response = $fb->get('/me?fields=id,name', '{access-token}');
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			$app->render(500, ['Status' => 'Login failed. '. $e->getMessage()  ]);
			exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			$app->render(500, ['Status' => 'Login failed. '. $e->getMessage()  ]);
			exit;
		}

		$user = $response->getGraphUser();
		if (!UserModel::findByProviderUserId($user['id'])) {
			//User does not exist, register
			register($user['name'], "Facebook", $user['id']);
		}

		$_SESSION['userId'] = UserModel::findByProviderUserId($user['id'])['userId'];
	}


}
