<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

# Alerta de versión
try {
  if (version_compare(phpversion(), '7.0.0', '<'))
    throw new Exception(true);
} catch (Exception $e) {
  die('La versión actual de <b>PHP</b> es <b>' . phpversion() . '</b> y como mínimo se require la versión <b>7.0.0</b>');
}

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
  Kint::$theme = 'aante-light';
  $startime = explode(" ",microtime());
  $startime = $startime[0] + $startime[1];
}

//------------------------------------------------

# Router para URL's amigables
$router = new Router;

//------------------------------------------------

# Activación del Firewall
!FIREWALL ?: new Firewall;

?>
