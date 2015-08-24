<?php
require_once('DatabaseConfiguration.php');
  
class Db {
  private static $instance = NULL;

  private function __construct() {}

  private function __clone() {}

  public static function getInstance() {

    if (!isset(self::$instance)) {
      $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
      self::$instance = new PDO('mysql:host='.MYSQL_HOST.';port='.MYSQL_PORT.';charset=utf8;dbname='.MYSQL_DATABASE, MYSQL_USER, MYSQL_PASSWORD, $pdo_options);
    }
    return self::$instance;
  }
}
