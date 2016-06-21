<?php

class Router {

  public $dir = __ROOT__;
  private $url = null;
  private $controller = null;
  private $method = null;
  private $id = null;

  public function __construct() {

    $this->url = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

    if($this->dir == '/' and strlen($this->url) > strlen($this->dir)) {
      $this->url[0] = '';
    } else {
      $this->url = explode($this->dir,$this->url);
      $this->url = $this->url[1];
    }

    if(!empty($this->url) and $this->url != $this->dir) {
      $this->url = explode('/',$this->url);

      $this->controller = Func::alphanumeric($this->url[0]) ? strtolower( $this->url[0] ) . 'Controller' : 'homeController';
      $this->method = array_key_exists(1,$this->url) ? strtolower($this->url[1]) : null;
      $this->id = array_key_exists(2,$this->url) ? $this->url[2] : null;
    } else {
      $this->controller = 'homeController';
    }

  }

  public function getController() : string {
    return $this->controller;
  }

  public function getMethod() {
    return $this->method;
  }

  public function getId() {
    return $this->id;
  }

}

?>
