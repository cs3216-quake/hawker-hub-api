<?php
require_once('DatabaseConfiguration.php');
  
class Db {
  private static $instance = NULL;

  private function __construct() {}

  private function __clone() {}

  public static function getInstance() {

    if (!isset(self::$instance)) {
      $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
      $now = new DateTime();
      $mins = $now->getOffset() / 60;
      $sgn = ($mins < 0 ? -1 : 1);
      $mins = abs($mins);
      $hrs = floor($mins / 60);
      $mins -= $hrs * 60;
      $offset = sprintf('%+d:%02d', $hrs*$sgn, $mins);
      self::$instance = new PDO('mysql:host='.MYSQL_HOST.';port='.MYSQL_PORT.';charset=utf8;dbname='.MYSQL_DATABASE, MYSQL_USER, MYSQL_PASSWORD, $pdo_options);
      self::$instance->exec("SET time_zone='$offset';");
    }
    return self::$instance;
  }
}
