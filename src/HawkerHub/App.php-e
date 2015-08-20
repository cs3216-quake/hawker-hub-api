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
        $app->get('/api/v1', function () use ($app) {
            $this->app->render(200, ['Status' => 'Running']);
		});

    }
}
