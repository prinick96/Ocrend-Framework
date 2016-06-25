<?php

define('INDEX_DIR',true);
require('../core/api_core.php');

if(DEBUG) {
  $app = new \Slim\App(['settings' => ['displayErrorDetails' => true]]);
  $c = $app->getContainer();
  $c['notAllowedHandler'] = function ($c) {
      return function ($request, $response, $methods) use ($c) {
          return $c['response']
              ->withStatus(405)
              ->withHeader('Allow', implode(', ', $methods))
              ->withHeader('Content-type', 'text/html')
              ->write('Method must be one of: ' . implode(', ', $methods));
      };
  };
} else {
  $app = new \Slim\App;
}


if($_GET) {
  include('http/get.php');
}
if($_POST) {
  include('http/post.php');
}

$app->run();

#http://www.slimframework.com/docs/objects/router.html
?>
