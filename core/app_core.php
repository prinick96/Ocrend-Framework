<?php

#definimos desde donde se accede a los controllers
define('IS_API',false);

#carga de constantes
require('core/config.php');

#implementación de autoloaders
require('core/kernel/Ocrend.php');
require('vendor/autoload.php');

$startime = microtime();
$startime = explode(" ",$startime);
$startime = $startime[0] + $startime[1];

?>
