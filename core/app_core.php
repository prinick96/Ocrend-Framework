<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

# Definimos desde donde se accede a los controllers
define('IS_API',false);

//------------------------------------------------

# Carga de configuración general
require('core/config.php');

//------------------------------------------------

# Implementación de autoloaders
require('core/kernel/Ocrend.php');
require('vendor/autoload.php');

//------------------------------------------------

# Test de velocidad de el Debug
if(DEBUG) {
  $startime = microtime();
  $startime = explode(" ",$startime);
  $startime = $startime[0] + $startime[1];
}

//------------------------------------------------

# Router para URL's amigables
$router = new Router;

?>
