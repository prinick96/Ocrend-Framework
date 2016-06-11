<?php

defined('INDEX_DIR') OR exit('Ocrend software says .i.');

$app->post('/',function($request, $response){
  $data = array('peticion' => 'POST');
  $response->withJson($data);

  return $response;
});

/*
  Registro de un usuario
  Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/register',function($request, $response){
  $reg = new Register();
  $response->withJson($reg->SignUp($_POST));

  return $response;
});


?>
