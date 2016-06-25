<?php

defined('INDEX_DIR') OR exit('Ocrend software says .i.');

final class Files {

  /**
    * Devuelve la extensión de un archivo cualquiera, da igual si es solo el nombre o la ruta con el nombre
    *
    * @param string $file_name: Nombre del archivo, da igual si es solo el nombre o la ruta con el nombre
    *
    * @return string con la extensión, devuelve un string '' si no existe información alguna acerca de la extensión
  */
  final public static function get_file_ext(string $file_name) : string {
    return pathinfo($file_name, PATHINFO_EXTENSION);
  }

  /**
    * Dice si un elemento es una imagen o no según su extensión
    *
    * @param string $file_name: Nombre del archivo, da igual si es solo el nombre o la ruta con el nombre
    *
    * @return true si es una imagen, false si no lo es
  */
  final public static function is_image(string $file_name) : bool {
    return in_array(self::get_file_ext($file_name),['jpg','png','jpeg','gif','JPG','PNG','JPEG','GIF']);
  }

  /**
    * Devuelve en un arreglo numérico, la ruta de todos los ficheros en un directorio filtrado por tipos
    *
    * @param string $dir: directorio completo
    * @param strnng $types: tipos de archivos a buscar, por defecto '' significa todos, se puede pasar por ejemplo 'jpg'
    *
    * @return array con las rutas de todos los ficheros encontrados, un array vacío si no encontró ficheros
  */
  final public static function get_files_in_dir(string $dir, string $types = '') : array {
    $array = array();
    if(is_dir($dir)) {
      foreach (glob($dir . '*' . $types) as $file) {
        $array[] = $file;
      }
    }
    return $array;
  }

  /**
    * Elimina de forma recursiva un directorio con su contenido
    *
    * @author brandonwamboldt
    *
    * @param string $dir: Directorio a borrar
    *
    * @return bool true si todo se borró con éxito
  */
  final public static function rm_dir(string $dir) {
    if(!file_exists($dir)) {
      return true;
    } else if (!is_dir($dir)) {
        throw new \RuntimeException('El "directorio" especificado no es un directorio.');
    }

    if(!is_link($dir)) {
      foreach (scandir($dir) as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $currentPath = $dir . '/' . $file;
        if (is_dir($currentPath)) {
            self::rmdir($currentPath);
        } elseif (!unlink($currentPath)) {
          throw new \RuntimeException('No se puede borrar ' . $currentPath);
        }
      }
    }

    return true;
  }

}

?>
