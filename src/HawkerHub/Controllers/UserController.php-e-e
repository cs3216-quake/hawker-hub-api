<?php

namespace HawkerHub\Controllers;

use \HawkerHub\Models\UserModel;
use \Facebook\Facebook;
use \Facebook\Exceptions\FacebookResponseException;
use \Facebook\Exceptions\FacebookSDKException;

require_once('FacebookCredentials.php');

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

	public function logout() {
		$fb = new Facebook([
			'app_id' => FB_APP_ID,
			'app_secret' => FB_SECRET,
			'default_graph_version' => 'v2.4',
			]);
		$app = \Slim\Slim::getInstance();
		if ($this->isLoggedIn()) {
			$self_url= sprintf('%s://%s%s',
				empty($_SERVER['HTTPS']) ? 'http' : 'https',
				$_SERVER['HTTP_HOST'], $_SERVER['SCRIPT_NAME']
				);
			$helper = $fb->getRedirectLoginHelper();
			$logout_url = $helper->getLogoutUrl($_SESSION['fb_access_token'], $self_url);
		    // Now we destroy the PHP session
			session_destroy();

		    // If it's desired to kill the session, also delete the session cookie.
		    // Note: This will destroy the session, and not just the session data!
			if (ini_get('session.use_cookies')) {
				$params = session_get_cookie_params();
				setcookie(session_name(), '', time() - 42000,
					$params['path'], $params['domain'],
					$params['secure'], $params['httponly']
					);
			}

		    // Redirect the user to the actual facebook logout URL
		} else {
			$app->render(500, ['Status' => 'Not logged in.' ]);
		}
	}

	public function isLoggedIn() {
		return !empty($_SESSION['fb_access_token']);
	}

	public function login() {
		$fb = new Facebook([
			'app_id' => FB_APP_ID,
			'app_secret' => FB_SECRET,
			'default_graph_version' => 'v2.4',
			]);
		$app = \Slim\Slim::getInstance();
		// verify with Facebook using the Facebook PHP SDK
		$helper = $fb->getJavaScriptHelper();
		// If user is not already logged in...
		if (empty($_SESSION['fb_access_token'])) {
			try {
				$accessToken = $helper->getAccessToken();
			} catch(FacebookResponseException $e) {
    			// When Graph returns an error
				echo 'Graph returned an error: ' . $e->getMessage();
				exit;
			} catch(FacebookSDKException $e) {
    			// When validation fails or other local issues
				echo 'Facebook SDK returned an error: ' . $e->getMessage();
				exit;
			}

			if (!isset($accessToken)) {
				$loginUrl = $helper->getLoginUrl($self_url);
				echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
				exit;
			}

  			// The OAuth 2.0 client handler helps us manage access tokens
			$oAuth2Client = $fb->getOAuth2Client();

 			// Get the access token metadata from /debug_token
			$tokenMetadata = $oAuth2Client->debugToken($accessToken);
			$tokenMetadata->validateAppId(FB_APP_ID);
			$tokenMetadata->validateExpiration();

			if (!$accessToken->isLongLived()) {
    			// Exchanges a short-lived access token for a long-lived one
				try {
					$accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
				} catch (FacebookSDKException $e) {
					echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
					exit;
				}
			}

			$_SESSION['fb_access_token'] = (string) $accessToken;
		}


	}


}

