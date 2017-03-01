<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

final class Arrays extends Twig_Extension {

  //------------------------------------------------

  /**
    * Dado un índice asociativo y un arreglo, devuelve el índice numérico correspondiente al asociativo
    *
    * @param string $index: Índice asociativo del arreglo
    * @param array $a: Arreglo a evaluar
    *
    * @return int el índice correspondiente, -1 si no existe el indice
  */
  final public static function get_key_by_index(string $index, array $a) : int {
    $i = 0;
    foreach ($a as $key => $val) {
      if($key == $index) {
        return (int) $i;
      }
      $i++;
    }
    return -1;
  }

  //------------------------------------------------

  /**
    * Elimina todos los elementos repetidos de un array
    * (string) '1' se considera igual a (int) 1
    *
    * @param array $a: Arreglo a evaluar
    *
    * @return array devuelve un arreglo sin elementos repetidos
    * http://stackoverflow.com/questions/8321620/array-unique-vs-array-flip
  */
  final public static function unique_array(array $a) : array {
    return array_keys(array_flip($a));
  }

  //------------------------------------------------

  /**
    * Evalúa si un arreglo es de tipo asociativo o no
    *
    * @param array $a: Arreglo a evaluar
    *
    * @return false si no lo es, true si lo es
  */
  final public static function is_assoc(array $a) : bool {
    if(sizeof($a) === 0) {
      return false;
    }

    return (bool) (array_keys($a) !== range(0, count($a) - 1));
  }

  //------------------------------------------------

  /**
    * Evalúa si un arreglo es secuencial (de índices numéricos)
    *
    * @param array $a: Arreglo a evaluar
    *
    * @return false si no lo es, true si lo es
  */
  final public static function is_numeric_array(array $a) : bool {
    $i = 0;
    foreach ($a as $key => $value) {
      if($key != $i) {
        return false;
      }
      $i++;
    }
    return true;
  }

  //------------------------------------------------

  /**
    * Obtiene de forma random un elemento de un arreglo
    *
    * @param array $a: Arreglo a evaluar
    *
    * @return mixed, elemento random dentro del arreglo
  */
  final public static function array_random_element(array $a) {
    return $a[array_rand($a)];
  }

  //------------------------------------------------

  /**
    * Se obtiene de Twig_Extension y sirve para que cada función esté disponible como etiqueta en twig
    *
    * @return array: Todas las funciones con sus respectivos nombres de acceso en plantillas twig
  */
  public function getFunctions() : array {
    return array(
      new Twig_Function('get_key_by_index', array($this, 'get_key_by_index')),
      new Twig_Function('unique_array', array($this, 'unique_array')),
      new Twig_Function('is_assoc', array($this, 'is_assoc')),
      new Twig_Function('is_numeric_array', array($this, 'is_numeric_array')),
      new Twig_Function('array_random_element', array($this, 'array_random_element'))
    );
  }

  //------------------------------------------------

  /**
    * Identificador único para la extensión de twig
    *
    * @return string: Nombre de la extensión
  */
  public function getName() : string {
    return 'ocrend_framework_helper_arrays';
  }

}


?>
