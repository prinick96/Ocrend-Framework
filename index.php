<?php

require('core/core.php');

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

?>
