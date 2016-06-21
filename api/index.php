<?php

define('INDEX_DIR',true);
require('../core/api_core.php');

if(DEBUG) {
  $app = new \Slim\App(['settings' => ['displayErrorDetails' => true]]);
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
