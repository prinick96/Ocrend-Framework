<?php

$app->put('/',function($request, $response){
  $response->write('PUT Respuesta');

  return $response;
});

?>
