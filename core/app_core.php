<?php

defined('INDEX_DIR') OR exit('Ocrend software says .i.');

#definimos desde donde se accede a los controllers
define('IS_API',false);

#carga de constantes
require('core/config.php');

#implementación de autoloaders
require('core/kernel/Ocrend.php');
require('vendor/autoload.php');

#test de velocidad para el Debug
if(DEBUG) {
  $startime = microtime();
  $startime = explode(" ",$startime);
  $startime = $startime[0] + $startime[1];
}

#router para URL's amigables
$router = new Router;

?>
