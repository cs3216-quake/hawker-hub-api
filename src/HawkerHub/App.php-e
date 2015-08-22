<?php

namespace HawkerHub;

use \Slim\Slim;
use \SlimJson\Middleware;

/**
 * Class App
 *
 * Main class of the REST API
 * @package HawkerHub
 */
class App {
	/**
	 *  Construct a new App instance
	 */
	public function __construct() {
		$this->app = new Slim();
		$this->setupMiddleWare();
		$this->addDefaultRoutes();
	}

	/**
	 *  Run the App instance
	 */
	public function run() {
		$this->app->run();
	}

	private $app;

	private function setupMiddleWare() {
		$this->app->add(new Middleware(array(
			'json.status' => true,
			'json.override_error' => true,
			'json.override_notfound' => true
		)));
	}

	private function addDefaultRoutes() {
		$app = $this->app;
		$app->group('/api', function() use ($app) {

			$app->get('/v1', function () use ($app) {
				$this->app->render(200, ['Status' => 'Running']);
			});

			$app->group('/user', function() use ($app) {

				$userController = new \HawkerHub\Controllers\UserController($app);
				$allPostVars = $this->app->request->post();

				$app->post('/register', function() use($allPostVars,$userController) {
					$displayName = $allPostVars['displayName'];
					$provider = $allPostVars['provider'];
					$providerUserId = $allPostVars['userId'];
					$providerAccessToken = $allPostVars['accessToken'];

					$userController->register($displayName,$provider,$providerUserId,$providerAccessToken);
				});

				$app->post('/login', function() use($allPostVars,$userController) {
					$providerUserId = $allPostVars['userId'];
					$providerAccessToken = $allPostVars['accessToken'];

					$userController->login($providerUserId,$providerAccessToken);
				});
			});

			$app->group('/item', function() use ($app) {
				$itemController = new \HawkerHub\Controllers\ItemController($app);

				// Get /api/item{?startAt,limit,orderBy,lat,lng}
				$app->get('', function() use ($app) {

				});

				// Post /api/item
				$app->post('', function($id) use ($app) {

				});

				// Route /api/item/{id}
				$app->group('/:id', function($id) use ($app) {

					// Get /api/item/{id}
					$app->get('', function($id) use ($app) {

					});

					// Route /api/item/{id}/like
					$app->group('/like', function($id) use ($app) {

						// Get
						$app->get('', function($id) use ($app) {

						});

						// Post
						$app->post('', function($id) use ($app) {

						});

					});

					// Route /api/item/{id}/comments
					$app->group('/comments', function($id) use ($app) {

						// Get
						$app->get('', function($id) use ($app) {

						});
						
						// Post
						$app->post('', function($id) use ($app) {

						});

					});

				});

			});
		});
	}
}
