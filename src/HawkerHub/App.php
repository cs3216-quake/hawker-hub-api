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
		$this->startSession();
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

	private function startSession() {
		session_start();
	}

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

			/*
			$app->group('/user', function() use ($app) {

				$userController = new \HawkerHub\Controllers\UserController();

				/*
				Do we need this?
				$app->post('/register', function() use($app,$userController) {
					$allPostVars = $app->request->post();
					$displayName = $allPostVars['displayName'];
					$provider = $allPostVars['provider'];
					$providerUserId = $allPostVars['userId'];

					$userController->register($displayName,$provider,$providerUserId);
				});


				$app->get('/login', function() use($app,$userController) {
					$userController->login();
				});

				$app->get('/logout', function() use($app,$userController) {
					$userController->logout();
				});
			});
			*/

			$app->group('/item', function() use ($app) {
				$itemController = new \HawkerHub\Controllers\ItemController();

				// Get /api/item{?startAt,limit,orderBy,lat,lng}
				$app->get('', function() use ($app,$itemController) {
					$allGetVars = $app->request->get();
					$startAt = @$allGetVars['startAt']? $allGetVars['startAt']: 0;
					$limit = @$allGetVars['limit']? $allGetVars['limit']: 15;

					if (@$allGetVars['orderBy'] && $allGetVars['orderBy'] == 'location') {
						//Sort by location
						$lat = $allGetVars['latitude'];
						$long = $allGetVars['longtitude'];
						$itemController->listFoodItemSortedByLocation($startAt,$limit,$lat,$long);
					} else {
						//Sort by most recent
						$itemController->listFoodItemSortedByMostRecent($startAt,$limit);
					}

				});

				// Post /api/item
				$app->post('', function() use ($app,$itemController) {
					$allPostVars = $app->request->post();

					$itemName = $allPostVars['itemName'];
					$photoURL = $allPostVars['photoURL'];
					$caption = $allPostVars['caption'];
					$longtitude = $allPostVars['longtitude'];
					$latitude = $allPostVars['latitude'];

					$itemController->createNewItem($itemName, $photoURL, $caption, $longtitude, $latitude);
				});

				// Route /api/item/{id}
				$app->group('/:id', function($id) use ($app,$itemController) {

					// Get /api/item/{id}
					$app->get('', function($id) use ($app,$itemController) {
						$itemController->findByItemId($id);
					});

					// Route /api/item/{id}/like
					$app->group('/like', function($id) use ($app) {

						// Get
						$app->get('', function($id) use ($app) {
							$likeController = new \HawkerHub\Controllers\LikeController($app);
							$likeController->listLikes($id);
						});

						// Post
						$app->post('', function($id) use ($app) {
							$likeController = new \HawkerHub\Controllers\LikeController($app);
							$likeController->insertLike($id);
						});

					});

					// Route /api/item/{id}/comment
					$app->group('/comment', function($id) use ($app) {
						// Get
						$app->get('', function($id) use ($app) {
							$commentController = new \HawkerHub\Controllers\CommentController($app);
							$commentController->listComments($id);
						});

						// Post
						$app->post('', function($id) use ($app) {
							$commentController = new \HawkerHub\Controllers\CommentController($app);
							$allPostVars = $app->request->post();
							$sanitizedMessage = htmlspecialchars($allPostVars['message'], ENT_QUOTES, 'UTF-8');
							$commentController->insertComment($id, $sanitizedMessage);
						});

					});

				});

			});

			$app->group('/image', function() use ($app) {

				$app->get('', function() use ($app) {

					$this->app->render(200, ['status' => 'image api working']);
				});

			});
		});
	}
}
