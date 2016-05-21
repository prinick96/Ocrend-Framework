<?php

$app->map(['GET', 'POST'], '/',function($request, $response){
  $response->write('MAP Respuesta (mixta get y post)');

  return $response;
});

?>
