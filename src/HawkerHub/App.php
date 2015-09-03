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
        if(!session_id()) {
            session_start();
            date_default_timezone_set('Asia/Singapore');
        }
    }

    private function setupMiddleWare() {
        $this->app->add(new Middleware(array(
            'json.status' => false,
            'json.override_error' => true,
            'json.override_notfound' => true
        )));
    }

    private function addDefaultRoutes() {
        $app = $this->app;
        $app->response->headers->set('Access-Control-Allow-Origin', '*');

        $app->get('/food/:itemId/', function($itemId) use ($app) {
            require_once('foodTemplate.php');
        });

        $app->group('/api', function() use ($app) {

            $app->group('/v1', function () use ($app) {

                $app->get('', function () use ($app) {
                    $app->render(200, ['Status' => 'Running']);
                });

                $app->group('/users', function() use ($app) {

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
                    */

                    $app->post('/deauthorize', function() use($app,$userController) {
                        $allPostVars = $app->request->post();
                        $signed_request = @$allPostVars['signed_request']?$allPostVars['signed_request']:"";
                        $userController->deauthorizeFacebooks($signed_request);
                    });

                    $app->post('/settings', function() use($app,$userController) {
                        $jsonData = $app->request->getBody();
                        $userController->updateSettings(json_decode($jsonData,true));
                    });

                    $app->get('/login', function() use($app,$userController) {
                        $userController->isLogin();
                    });

                    $app->post('/login', function() use($app,$userController) {
                        $userController->login();
                    });

                    $app->get('/logout', function() use($app,$userController) {
                        $userController->logout();
                    });

                    $app->group('/:userId', function() use($app,$userController) {
                        $app->get('', function($userId) use($app,$userController) {
                            $userController->getUserInformation($userId);
                        });

                        $app->group('/item', function() use($app,$userController) {
                            $app->get('/recent', function($userId) use($app,$userController) {
                                $allGetVars = $app->request->get();
                                $startAt = @$allGetVars['startAt']? $allGetVars['startAt']: 0;
                                $limit = @$allGetVars['limit']? $allGetVars['limit']: 15;

                                $userController->getUserItems($userId,$startAt,$limit);
                            });
                        });
                    });
                });

                $app->group('/item', function() use ($app) {
                    $itemController = new \HawkerHub\Controllers\ItemController();

                    // Get /api/item{?startAt,limit,orderBy,lat,lng}
                    $app->get('', function() use ($app,$itemController) {
                        $allGetVars = $app->request->get();
                        $startAt = @$allGetVars['startAt']? $allGetVars['startAt']: 0;
                        $limit = @$allGetVars['limit']? $allGetVars['limit']: 15;
                        $orderBy = @$allGetVars['orderBy']? $allGetVars['orderBy']: "id";
                        $longtitude = @$allGetVars['longtitude']? $allGetVars['longtitude']: 0;
                        $latitude = @$allGetVars['latitude']? $allGetVars['latitude']: 0;

                        $itemController->listFoodItem($orderBy, $startAt, $limit, $latitude, $longtitude, '');
                    });

                    // Post /api/item
                    $app->post('', function() use ($app,$itemController) {
                        $jsonData = $app->request->getBody();
                        $allPostVars = json_decode($jsonData,true);

                        $itemName = $allPostVars['itemName'];
                        $photoURL = $allPostVars['photoURL'];
                        $caption = $allPostVars['caption'];
                        $longtitude = $allPostVars['longtitude'];
                        $latitude = $allPostVars['latitude'];

                        $itemController->createNewItem($itemName, $photoURL, $caption, $longtitude, $latitude);
                    });

                    // Route /api/v1/item/search{?keyword=<keyword>}
                    $app->group('/search', function() use ($app, $itemController) {
                        // GET
                        $app->get('', function() use ($app, $itemController) {
                            $allGetVars = $app->request->get();
                            $keyword = @$allGetVars['keyword']? $allGetVars['keyword']:'';
                            $startAt = @$allGetVars['startAt']? $allGetVars['startAt']: 0;
                            $limit = @$allGetVars['limit']? $allGetVars['limit']: 15;
                            $orderBy = @$allGetVars['orderBy']? $allGetVars['orderBy']: "id";
                            $itemController->listFoodItem($orderBy, $startAt, $limit, 0.0, 0.0, $keyword);
                        });
                    });

                    // Route /api/item/{id}
                    $app->group('/:id', function() use ($app,$itemController) {

                        // Get /api/item/{id}
                        $app->get('', function($id) use ($app,$itemController) {
                            $itemController->findByItemId($id);
                        });

                        $app->delete('', function($id) use ($app,$itemController) {
                            $itemController->deleteItem($id);
                        });

                        // Route /api/item/{id}/like
                        $app->group('/like', function() use ($app) {
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

                            // Delete
                            $app->delete('', function($id) use ($app) {
                                $likeController = new \HawkerHub\Controllers\LikeController($app);
                                $likeController->deleteLike($id);
                            });

                        });

                        // Route /api/item/{id}/comment
                        $app->group('/comment', function() use ($app) {
                            // Get
                            $app->get('', function($id) use ($app) {
                                $commentController = new \HawkerHub\Controllers\CommentController($app);
                                $commentController->listComments($id);
                            });

                            // Post
                            $app->post('', function($id) use ($app) {
                                $commentController = new \HawkerHub\Controllers\CommentController($app);
                                $allPostVars = $app->request->post();
                                $message = @$allPostVars['message']?$allPostVars['message']:""; 
                                $commentController->insertComment($id, $message);
                            });

                            // Delete
                            $app->delete('/:commentId', function($id, $commentId) use ($app) {
                                $commentController = new \HawkerHub\Controllers\CommentController($app);
                                $commentController->deleteComment($id,$commentId);
                            });

                        });
                    });

                    // Route /api/item/photo
                    $app->group('/photo', function() use ($app) {
                        $photoController = new \HawkerHub\Controllers\PhotoController();

                        // Get /api/item/photo/{photo}
                        // Note: Route maps to uploads/{photo}
                        $app->get('/:link', function($link) use ($app, $photoController) {
                            $photoController->downloadPhoto($link);
                        });

                        // POST /api/item/photo
                        $app->post('', function() use ($app, $photoController) {
                            $rawFiles = $_FILES;
                            $photoController->uploadPhoto($app->request->getUrl(), $rawFiles);
                        })->name('photo');
                    });

                });
            });
        });

    }
}
