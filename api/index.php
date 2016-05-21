<?php

require('../core/api_core.php');
$app = new \Slim\App;

//http://www.slimframework.com/docs/objects/router.html
include('http/get.php');
include('http/post.php');
include('http/delete.php');
include('http/put.php');
include('http/map.php');

$app->run();

?>
