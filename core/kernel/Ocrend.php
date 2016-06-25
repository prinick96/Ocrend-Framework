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
  if(file_exists($exec)) {
    require($exec);
  }
}

//------------------------------------------------

# Autoloader de modelos
function __models_autoload(string $model) {
  $model = ROOT_DIR . 'core/models/'. $model .'.php';
  if(file_exists($model)) {
    require_once($model);
  }
}

//------------------------------------------------

# Activación del Firewall
!FIREWALL ?: new Firewall;

?>
