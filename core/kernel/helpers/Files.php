<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

final class Files extends Twig_Extension {

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
    * Borra un archivo en un directorio
    *
    * @param string $route: Ruta del fichero
    *
    * @return bool true si borró el fichero, false si no (porque no existía)
  */
  final public static function delete_file(string $route) : bool {
    if(file_exists($route)) {
      unlink($route);

      return true;
    }

    return false;
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
    return (bool) in_array(self::get_file_ext($file_name),['jpg','png','jpeg','gif','JPG','PNG','JPEG','GIF']);
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
            self::rm_dir($currentPath);
        } elseif (!unlink($currentPath)) {
          throw new \RuntimeException('No se puede borrar ' . $currentPath);
        }
      }
    }

    return true;
  }

  //------------------------------------------------

  /**
    * Devuelve la cantidad de imágenes contenidas dentro de un directorio
    *
    * @param string $dir: Directorio en donde están las imagenes
    *
    * @return bool true si todo se borró con éxito
  */
  final public static function images_in_dir(string $dir) : int {
    return sizeof(glob($dir . '{*.jpg,*.gif,*.png,*.gif,*.jpeg,*.JPG,*.GIF,*.PNG,*.JPEG}',GLOB_BRACE));
  }

  //------------------------------------------------

  /**
    * Copia todos los ficheros de un directorio a un directorio nuevo, si el directorio nuevo no existe, es creado.
    * Si en el directorio nuevo existe un archivo con el mismo nombre de alguno en el viejo, este será sobreescrito.
    *
    * @param string $old_dir: Ruta del directorio viejo (de donde se moverán los ficheros)
    * @param string $new_dir: Ruta del directorio nuevo (hacia donde se moverán los ficheros)
    * @param bool $only_images: Pasar como TRUE, si sólo quiere pasar imagenes
    * @param bool $delete_old: Pasar como TRUE, si se quiere borrar todo el contenido del viejo directorio al pasarse
    *
    * @return void
  */
  final public static function move_from_dir(string $old_dir, string $new_dir, bool $only_images = false, bool $delete_old = false) {

    self::create_dir($new_dir);

    foreach(glob($new_dir . ($only_images ? '{*.jpg,*.gif,*.png,*.gif,*.jpeg,*.JPG,*.GIF,*.PNG,*.JPEG}' : '*'),GLOB_BRACE) as $file) {
      $name = explode('/',$file);
      $name = end($file);

      if(file_exists($new_dir . $name)) {
          unlink($new_dir . $name);
      }

      copy($file,$new_dir . $name);

      if($delete_old) {
        unlink($file);
      }
    }
  }

  //------------------------------------------------

  /**
    * Carga un fichero desde una ruta temporal (con $_FILES)
    *
    * @param string $tempFile: Directorio temporal ($_FILES['nombre']['tmp_name'])
    * @param string $name: Nombre que se le quiere dar al archivo (Ej: $_FILES['nombre']['name'] ó imagen.jpg)
    * @param string $dir: Directorio a subir
    * @param bool $sobr: Si se pasa TRUE, sobrescribe archivos con el mismo nombre, si no, copia el archivo
    * de conflicto con un nombre aleatorio para no sobrescribir nada.
    *
    * @return bool true si subió el archivo, false si no
  */
  final public static function upload_file(string $name, string $tempFile, string $dir, bool $sobr = false) : bool {

    if(move_uploaded_file($tempFile,(file_exists($dir . $name) && !$sobr) ? ($dir . time() . $name) : ($dir . $name))) {
      return true;
    }

    return false;
  }

  //------------------------------------------------

  /**
    * Se obtiene de Twig_Extension y sirve para que cada función esté disponible como etiqueta en twig
    *
    * @return array: Todas las funciones con sus respectivos nombres de acceso en plantillas twig
  */
  public function getFunctions() : array {
    return array(
      new Twig_Function('images_in_dir', array($this, 'images_in_dir')),
      new Twig_Function('get_files_in_dir', array($this, 'get_files_in_dir')),
      new Twig_Function('date_file', array($this, 'date_file')),
      new Twig_Function('file_size', array($this, 'file_size')),
      new Twig_Function('is_image', array($this, 'is_image')),
      new Twig_Function('get_file_ext', array($this, 'get_file_ext'))
    );
  }

  //------------------------------------------------

  /**
    * Identificador único para la extensión de twig
    *
    * @return string: Nombre de la extensión
  */
  public function getName() : string {
    return 'ocrend_framework_helper_files';
  }

}

?>
