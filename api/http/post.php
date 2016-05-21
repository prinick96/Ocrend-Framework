<?php

$app->post('/',function($request, $response){
  $data = array('peticion' => 'POST');
  $response->withJson($data);

  return $response;
});


?>
