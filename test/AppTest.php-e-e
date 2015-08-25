<?php

namespace Test;

$prev = error_reporting(0);
session_start();
error_reporting($prev);

use Slim\Environment;

class AppTest extends \PHPUnit_Framework_TestCase
{

	public function request($method, $path, $options = array())
	{
        // Capture STDOUT
		ob_start();

		$loader = require 'vendor/autoload.php';
		$loader->set('HawkerHub\\', __DIR__.'/../src/');
		chdir("src");
        // Prepare a mock environment
		Environment::mock(array_merge(array(
			'REQUEST_METHOD' => $method,
			'PATH_INFO' => $path,
			'SERVER_NAME' => '192.168.59.103',
			), $options));


		$app = new \HawkerHub\App();
		$this->app = $app;
		$this->app->run();
		$this->request = $this->app->request();
		$this->response = $this->app->response();

        // Return STDOUT
		return ob_get_clean();
	}

	public function get($path, $options = array())
	{
		$this->request('GET', $path, $options);
	}

	public function testVersion()
	{
		$this->get('/api/v1');
		$this->assertEquals('200', $this->response->status());
		$this->assertEquals('{"Status":"Running"}', $this->response->body());
	}

	public function testUsersSearch() {
		$this->get('/api/v1/item');
		$this->assertEquals('200', $this->response->status());
		
	}

}
