<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

final class Arrays {

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
}


?>
