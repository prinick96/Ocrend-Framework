<?php

defined('INDEX_DIR') OR exit('Ocrend software says .i.');

/**
  * Inicio de Sesión
  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->get('/login',function($request, $response) {

  $login = new Login();
  $response->withJson($login->SignIn($_GET['user'],$_GET['pass']));

  return $response;
});
