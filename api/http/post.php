<?php

defined('INDEX_DIR') OR exit('Ocrend software says .i.');

/**
  * Registro de un usuario
  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/register',function($request, $response){

  $reg = new Register();
  $response->withJson($reg->SignUp($_POST));

  return $response;
});
