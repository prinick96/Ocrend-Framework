<?php

spl_autoload_register('__kernel_autoload');
spl_autoload_register('__models_autoload');

define('ROOT_DIR', IS_API ? '../' : '');

function __kernel_autoload(string $exec) {
  $exec = ROOT_DIR . 'core/kernel/'. $exec .'.php';
  if(file_exists($exec)) {
    require($exec);
  }
}

function __models_autoload(string $model) {
  $model = ROOT_DIR . 'core/models/'. $model .'.php';
  if(file_exists($model)) {
    require_once($model);
  }
}

?>
