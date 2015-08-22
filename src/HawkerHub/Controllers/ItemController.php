<?php

namespace HawkerHub\Controllers;

use \HawkerHub\Models\ItemModel;

/**
 * Class RegisterController
 *
 * Controller class for all registration functions
 * @package HawkerHub
 **/
class ItemController extends \HawkerHub\Controllers\Controller {

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

}

