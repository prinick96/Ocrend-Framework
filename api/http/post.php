<?php

$app->post('/',function($request, $response){
  $data = array('peticion' => 'POST');
  $response->withJson($data);
  $response->withStatus(200);

  return $response;
});


?>
