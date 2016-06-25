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

/**
  * Inicio de Sesión
  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/login',function($request, $response) {

  $login = new Login();
  $response->withJson($login->SignIn($_POST));

  return $response;
});

/**
	* Recuperación de contraseña perdida
	* @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->post('/lostpass',function($request, $response) {

	$model = new Lostpass;
	$response->withJson($model->RepairPass($_POST));

	return $response;
});
