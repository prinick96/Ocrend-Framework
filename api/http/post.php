<?php

$app->post('/',function($request, $response){
  $data = array('peticion' => 'POST');
  $response->withJson($data);

  return $response;
});

$app->post('/register',function($request, $response){
  $reg = new Register();
  $response->withJson($reg->SignUp($_POST));

  return $response;
});


?>
