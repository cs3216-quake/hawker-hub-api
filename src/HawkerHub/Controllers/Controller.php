<?php

namespace HawkerHub\Controllers;

class Controller {
	private $model;

	public function __construct($model) {
		$this->model = $model;
	}
}
