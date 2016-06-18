<?php

/**
 * OCREND Framework - MVC Architecture for Web Applications
 * PHP Version 7.0.3
 * @package OCREND Framework
 * @link http://www.ocrend.com/framework
 * @author Brayan Narváez (Prinick) <prinick@ocrend.com>
 * @copyright 2016 - Ocrend Software
*/

define('INDEX_DIR',true);
require('core/app_core.php');

if(isset($_GET['view'])) {
  $Controller = strtolower($_GET['view']) . 'Controller';
  if(!is_readable('core/controllers/' . $Controller . '.php')) {
    $Controller = 'errorController';
  }
} else {
  $Controller = 'homeController';
}

require('core/controllers/' . $Controller . '.php');
new $Controller;

(!DEBUG and !IS_API) ?: new Debug($startime);

?>
