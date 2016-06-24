<?php

defined('INDEX_DIR') OR exit('Ocrend software says .i.');

class Router {

  public $dir = __ROOT__;
  private $url = null;
  private $controller = null;
  private $method = null;
  private $id = null;

  private $routes = array(
    '/controller' => 'alphanumeric',
    '/method' => 'none',
    '/id' => 'int'
  );

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

  public function setRoute(string $name, string $type = 'none') {
    if(!in_array($type,['alphanumeric','letters','int','float'])) {
      $type = 'none';
    }
    $this->routes[$name] = $type;
  }

  public function getRoute(string $name) {

    $index = Func::get_key_by_index($name,$this->routes);

    if($index >= 3 and array_key_exists($index,$this->url)) {
      switch ($this->routes[$name]) {
        case 'alphanumeric':
          return Func::alphanumeric($this->url[$index]) ? strtolower($this->url[$index]) : null;
        break;
        case 'letters':
          return Func::only_letters($this->url[$index]) ? strtolower($this->url[$index]) : null;
        break;
        case 'int':
          return is_numeric($this->url[$index]) ? intval($this->url[$index]) : null;
        break;
        case 'float':
          return is_numeric($this->url[$index]) ? floatval($this->url[$index]) : null;
        break;
        default:
          return $this->url[$index];
        break;
      }
    }

    return null;
  }

}

?>
