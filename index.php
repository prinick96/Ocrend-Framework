<?php

/**
 * OCREND Framework - MVC Architecture for Web Applications
 * PHP Version 7.0.2
 * @package OCREND Framework
 * @link http://www.ocrend.com/
 * @author Brayan Narváez (Prinick) <princk093@gmail.com> <prinick@ocrend.com>
 * @copyright 2016 - Ocrend Software
 */

/*
  Si existe algún tipo de error de permisos derivado al Firewall ó Twig
  ~$ sudo chmod -R 777 /ruta/en/donde/esta/el/framework
*/

#Falta integrar sistema de plugins
require('core/app_core.php');

if(isset($_GET['view'])) {
  $Controller = strtolower($_GET['view']) . 'Controller';
  if(!is_readable('core/controllers/' . $Controller . '.php')) {
    $Controller = 'errorController';
  }
} else {
  $Controller = 'indexController';
}

require('core/controllers/' . $Controller . '.php');
new $Controller;

(!DEBUG and !IS_API) ?: new Debug($startime);

?>
