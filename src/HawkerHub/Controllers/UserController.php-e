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

	public function __construct() {
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

	public function login() {
		$app = \Slim\Slim::getInstance();
		// verify with Facebook using the Facebook PHP SDK
		
		$fb = new Facebook([
			'app_id' => '1466024120391100',
			'app_secret' => 'fbpassword',
			'default_graph_version' => 'v2.2',
			]);

		$helper = $fb->getJavaScriptHelper();


		print $_SESSION['fb_access_token'];
		try {
			$accessToken = $helper->getAccessToken();
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
  			// When Graph returns an error
			echo 'Graph returned an error: ' . $e->getMessage();
			$app->render(500, ['Status' => 'Login failed.' ]);
			return;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
 			 // When validation fails or other local issues
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			$app->render(500, ['Status' => 'Login failed.' ]);
			return;
		}

		if (! isset($accessToken)) {
			echo 'No cookie set or no OAuth data could be obtained from cookie.';
			$app->render(500, ['Status' => 'Login failed.' ]);
			return;
		}

		var_dump($accessToken->getValue());

		$_SESSION['fb_access_token'] = (string) $accessToken;


		//if (UserModel::findByProviderUserId($providerUserId)) {
			//User exists
			
		//} else {
			//User does not exist, register

		//}
	}


}

