<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

final class Files {

  //------------------------------------------------

  /**
    * Devuelve un string con el contenido de un archivo
    *
    * @param string $dir: Directorio del archivo a leer
    *
    * @return string con contenido del archivo
  */
  final public static function read_file(string $dir) : string {
    $lines = '';
    $f = new SplFileObject($dir);
    while (!$f->eof()) {
        $lines .= $f->fgets();
    }
    return (string) $lines;
  }

  //------------------------------------------------

  /**
    * Escribe un string completo en un archivo, si este no existe lo crea
    *
    * @param string $dir: Directorio del archivo escribir/crear
    * @param string $content: Contenido a escribir
    *
    * @return catidad de bytes escritos en el archivo
  */
  final public static function write_file(string $dir, string $content) : int {
    $f = new SplFileObject($dir,'w');
    return (int) $f->fwrite($content);
  }

  //------------------------------------------------

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

  //------------------------------------------------

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

  //------------------------------------------------

  /**
    * Devuelve el tamaño en Kbytes de un fichero
    *
    * @param string $file: path del fichero
    *
    * @return int con el tamaño del fichero
  */
  final public static function file_size(string $file) : int {
  	return round(filesize($file)*0.0009765625, 1);
  }

  //------------------------------------------------

  /**
    * Devuelve la fecha y hora exacta de creación de un fichero
    *
    * @param string $file: path del fichero
    *
    * @return string con la fecha del fichero en el formato d-m-y h:i:s
  */
  final public static function date_file(string $file) : string {
  	return date('d-m-Y h:i:s', filemtime($file));
  }

  //------------------------------------------------

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

  //------------------------------------------------

  /**
    * Crea un directorio
    *
    * @param string $dir: Directorio a crear
    * @param int $permisos: Permisos del directorio a crear, por defecto es "todos los permisos"
    *
    * @return true si fue creado con éxito, false si el directorio ya existía o hubo algún error
  */
  final public static function create_dir(string $dir, int $permisos = 0777) : bool {
    if(is_dir($dir)) {
      return false;
    } else {
      return (bool) mkdir($dir,$permisos,true);
    }
  }

  //------------------------------------------------

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
