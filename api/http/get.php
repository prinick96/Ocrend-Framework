<?php

$app->get('/',function($request, $response){
  $response->write('GET Respuesta');

  return $response;
});

$app->get('/hola/{name}', function ($request, $response, $args) {
  $response->write('Hola ' . $args['name']);

  return $response;
});

?>
