<?php

#definimos desde donde se accede a los controllers
define('IS_API',true);

#carga de constantes
require('../core/config.php');

#implementación de autoloaders
require('../core/kernel/Ocrend.php');
require('../vendor/autoload.php');

#implementación de funciones
require('../core/functions/general_functions.php');

?>
