<?php

$app->get('/',function($request, $response){
  $response->write('GET Respuesta');
  $response->withStatus(200);

  return $response;
});

$app->get('/hola/{name}', function ($request, $response, $args) {
  $response->write('Hola ' . $args['name']);
  $response->withStatus(200);

  return $response;
});

?>
