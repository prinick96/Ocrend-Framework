<?php

defined('INDEX_DIR') OR exit('Ocrend software says .i.');

final class Helper {

  /**
    * const ROUTE: Constante que indica la ruta según desde donde se llame el loader, desde la API REST o desde la Aplicación
    *
  */
  const ROUTE = IS_API ? '../core/kernel/helpers/' : 'core/kernel/helpers/';

  /**
    * Carga de forma estática un helper alojado en la carpeta helpers del kernel para su posterior uso
    *
    * @param string $helper: Nombre del helper a cargar
    *
    * @return void
  */
  final static public function load(string $helper) {

    $file = self::ROUTE . ucwords($helper) . '.php';
    if(file_exists($file)) {
      include_once($file);
    } else {
      trigger_error('El helper ' . $helper . ' no existe en la librería de helpers.', E_USER_ERROR);
    }
  }

}

?>
