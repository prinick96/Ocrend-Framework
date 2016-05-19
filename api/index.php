<?php

require('../core/api_core.php');
$app = new \Slim\App;

include('http/get.php');
include('http/post.php');

$app->run();

?>
