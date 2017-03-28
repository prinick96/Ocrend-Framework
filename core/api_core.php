<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

# Definimos desde donde se accede a los controllers
define('IS_API',true);

//------------------------------------------------

# Carga de configuración general
require('../core/config.php');

//------------------------------------------------

# Implementación de autoloaders
require('../core/kernel/Ocrend.php');
require('../vendor/autoload.php');

//------------------------------------------------

# Router para URL's amigables
$router = new Router;

//------------------------------------------------

# Activación del Firewall
!FIREWALL ?: new Firewall;

?>
