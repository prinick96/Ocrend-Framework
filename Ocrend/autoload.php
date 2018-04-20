<?php

spl_autoload_register('__ocrend_autoload');

function __ocrend_autoload(string $class) {
    $class = ___ROOT___ . str_replace('\\', '/', $class);
    if (is_readable($class . '.php')) {
        require_once $class . '.php';
    }
}