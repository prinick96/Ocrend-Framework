<?php

spl_autoload_register('__kernel_autoload');
spl_autoload_register('__models_autoload');

//------------------------------------------------

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

# ¿En donde estoy?
define('ROOT_DIR', IS_API ? '../' : '');

//------------------------------------------------

# Autoloader de elementos en el Kernel
function __kernel_autoload(string $exec) {
  $exec = ROOT_DIR . 'core/kernel/'. $exec .'.php';
  if(is_readable($exec)) {
    require($exec);
  }
}

//------------------------------------------------

# Autoloader de modelos
function __models_autoload(string $model) {
  $model = ROOT_DIR . 'core/models/'. $model .'.php';
  if(is_readable($model)) {
    require_once($model);
  }
}

?>
