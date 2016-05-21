<?php

$app->delete('/',function($request, $response){
  $response->write('DELETE Respuesta');

  return $response;
});

?>
