<?php

namespace HawkerHub\Controllers;

use \HawkerHub\Models\UserModel;
use \Facebook\Facebook;
use \Facebook\Exceptions\FacebookResponseException;
use \Facebook\Exceptions\FacebookSDKException;
use \Facebook\SignedRequest;

require_once('FacebookCredentials.php');

/**
 * Class RegisterController
 *
 * Controller class for all registration functions
 * @package HawkerHub
 **/
class UserController extends \HawkerHub\Controllers\Controller {

	private $fb;

	public function __construct() {
		$this->fb = new Facebook([
			'app_id' => FB_APP_ID,
			'app_secret' => FB_SECRET,
			'cookie' => true
			]);
	}

	public function canViewItem($itemId) {
		$facebookFriendsId = $this->getAllFacebookFriendsId();
        $ownUserId = @$_SESSION['userId']?$_SESSION['userId']:"";

        return UserModel::canViewItem($itemId, $ownUserId, $facebookFriendsId);
	}

	public function canViewItemsByUserId($userId) {
		$facebookFriendsId = $this->getAllFacebookFriendsId();
        $ownUserId = @$_SESSION['userId']?$_SESSION['userId']:"";

        return UserModel::canViewItemsByUserId($userId, $ownUserId, $facebookFriendsId);
	}

	public function getAllFacebookFriendsId() {

		if ($this->isLoggedIn()) {
			$userCount = UserModel::getUserCount();

			$response = $this->fb->get('/me?fields=friends.limit('.$userCount.')', $_SESSION['fb_access_token']);
			$jsonData = json_decode($response->getBody(),true);
			$friends = $jsonData['friends']['data'];
			$friendsId = array();
			foreach ($friends as $friend) {
				array_push($friendsId, $friend['id']);
			}
			return $friendsId;
		} else {
			return [];
		}
	}

	public function deauthorizeFacebooks($signed_request) {
		$app = \Slim\Slim::getInstance();
		try {
			$signedRequest = new SignedRequest($this->fb->getApp(),$signed_request);
			// Get the user ID
			$providerUserId = $signedRequest->getUserId();
			$success = UserModel::deleteUserWithProviderUserId($providerUserId);
			if ($success<1) {
				$app->render(500, ['Status' => 'An error occured.' ] );
				return;
			}

			$app->render(204);

		} catch (\Exception $e) {
			$app->render(500, ['Status' => 'An error occured.' ] );
		}
	}

	public function getUserInformation($userId) {
		$app = \Slim\Slim::getInstance();
		if($this->isLoggedIn()) {
		$user = UserModel::findByUserId($userId);
		if (!$user) {
			$app->render(500, ['Status' => 'userId does not exist.' ]);
		} else {
			$this->getAllFacebookFriends();
			$app->render(200, (array) $user );
		}
		} else {
			$app->render(401, ['Status' => 'Not logged in.' ]);
		}
	}

	public function getUserItems($userId, $startAt = 0, $limit = 15) {
		$app = \Slim\Slim::getInstance();
		if($this->isLoggedIn()) {
			$items = UserModel::getItemsFromUserId($userId, $startAt, $limit, $_SESSION['userId'], $this->getAllFacebookFriendsId());
			if (!$items) {
				$app->render(500, ['Status' => 'userId does not exist.' ]);
			} else {
				$app->render(200, $items);
			}
		} else {
			$app->render(401, ['Status' => 'Not logged in.' ]);
		}
	}

	public function updateSettings($data) {
		$app = \Slim\Slim::getInstance();
		$privacy = $data['privacy'] == "public"? 1 : 0;
		if($this->isLoggedIn()) {
			$success = UserModel::updateSettings($_SESSION['userId'],$privacy);
			if (!$success) {
				$app->render(500, ['Status' => 'An error occured while updating user settings.' ]);
			} else {
				$app->render(200, ['Status' => 'Update successfully']);
			}
		} else {
			$app->render(401, ['Status' => 'Not logged in.' ]);
		}
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
		$app = \Slim\Slim::getInstance();
		if ($this->isLoggedIn()) {
			$this->destroySession();
			$app->render(200, ['Status' => 'Successfully logged out.' ]);
		} else {
			$app->render(401, ['Status' => 'Not logged in.' ]);
		}
	}

	public function isLoggedIn() {
		if (@$_SESSION['fb_access_token']&&@$_SESSION['userId']) {
			try {
				$oAuth2Client = $this->fb->getOAuth2Client();

 				// Get the access token metadata from /debug_token
				$tokenMetadata = $oAuth2Client->debugToken(@$_SESSION['fb_access_token']);
				$tokenMetadata->validateAppId(FB_APP_ID);
				$tokenMetadata->validateExpiration();
				if (!$tokenMetadata->getIsValid()) {
					$this->destroySession();
					return false;
				}
			} catch (FacebookSDKException $e) {
				$this->destroySession();
				return false;
			}
			return true;
		} else {
			return false;
		}
	}

	public function isLogin() {
		$app = \Slim\Slim::getInstance();
		if ($this->isLoggedIn()) {
			$app->render(200, ['Status' => 'Already logged in.' ]);
		} else {
			$app->render(200, ['Status' => 'Not logged in.' ]);
		}
	}

	public function login() {
		$app = \Slim\Slim::getInstance();

		// verify with Facebook using the Facebook PHP SDK
		// If user is not already logged in...
		if (!$this->isLoggedIn()) {
			$helper = $this->fb->getJavaScriptHelper();
			try {
				$accessToken = $helper->getAccessToken();
			} catch(FacebookResponseException $e) {
    			// When Graph returns an error
				$app->render(500, ['Status' => 'Login failed. '.$e->getMessage() ]);
				return;
			} catch(FacebookSDKException $e) {
    			// When validation fails or other local issues
				$app->render(500, ['Status' => 'Login failed. '.$e->getMessage() ]);
				return;
			}
			if (!@$accessToken) {
				$app->render(500, ['Status' => 'Login failed', 'sess' => $_SESSION['fb_access_token']]);
				return;
			}

  			// The OAuth 2.0 client handler helps us manage access tokens
			$oAuth2Client = $this->fb->getOAuth2Client();

 			// Get the access token metadata from /debug_token
			$tokenMetadata = $oAuth2Client->debugToken($accessToken);
			$tokenMetadata->validateAppId(FB_APP_ID);
			$tokenMetadata->validateExpiration();
			if (!$accessToken->isLongLived()) {
    			// Exchanges a short-lived access token for a long-lived one
				try {
					$accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
				} catch (FacebookSDKException $e) {
					$app->render(500, ['Status' => 'Login failed. '.$e->getMessage() ]);
					return;
				}
			}

			$_SESSION['fb_access_token'] = (string) $accessToken;

			try {
			  // Returns a `Facebook\FacebookResponse` object
				$response = $this->fb->get('/me?fields=id,name', $_SESSION['fb_access_token']);
			} catch(FacebookResponseException $e) {
				$app->render(500, ['Status' => 'Login failed.' ]);
				return;
			} catch(FacebookSDKException $e) {
				$app->render(500, ['Status' => 'Login failed.' ]);
				return;
			}

			$user = $response->getGraphUser();

			if (!UserModel::findByProviderUserId($user['id'])) {
				//User does not exist, register
				$this->register($user['name'], 'Facebook', $user['id']);
			}

			$user = UserModel::findByProviderUserId($user['id']);
			$_SESSION['userId'] = $user->userId;

			$app->render(200, (array) $user);
			return;
		} else {
			$app->render(200, ['Status' =>  'Already logged in.' ]);
		}
	}

	private function destroySession() {
		session_destroy();

	    // If it's desired to kill the session, also delete the session cookie.
	    // Note: This will destroy the session, and not just the session data!
		if (ini_get('session.use_cookies')) {
			$params = session_get_cookie_params();
			setcookie(
				session_name(),
				'',
				time() - 42000,
				$params['path'], $params['domain'],
				$params['secure'], $params['httponly']
			);
		}
	}
}
