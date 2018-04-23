<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ocrend\Kernel\Helpers;

/**
 * Helper con funciones útiles para el tratamiento de arreglos.
 *
 * @author Brayan Narváez <prinick@ocrend.com>
 */

class Arrays extends \Twig_Extension {

  /**
   * Suma los contenidos del segundo arreglo en el primer arreglo, en las coincidencias de llaves.
   *
   * Si el primer arreglo está vacío, copiará todo el segundo arreglo en el primero. 
   *
   * Si el segundo arreglo está vacío, copiará todo el primer arreglo en el segundo.
   *
   * Si el arreglo $a tiene una estructura distinta a $b, la estructura de $a queda intacta y sólamente,
   * se hará sumas en las posiciones donde haya coincidencias de llaves.
   *
   * El valor null será tomado como cero.
   *
   * @param array $a: Primer arreglo
   * @param array $b: Segundo arreglo
   *
   * @throws RuntimeException si los arreglos son de distinto nivel
   * @throws RuntimeException si en la posición de coincidencia no se puede realizar una operación de suma
   *
   * @return array fusión de los dos arreglos, dejando intactal estructura de $a
   */
  public static function arrays_sum(array $a, array $b) : array {
    # Si alguno está vacío
    if (sizeof($a) == 0) {
      return $b;
    } else if (sizeof($b) == 0) {
      return $a;
    }

    # Recorrer el segundo arreglo
    foreach ($b as $llave => $contenido) {
      # Verificar que no hay desnivel
      if (!is_array($a[$llave]) && !is_array($b[$llave])) {
        $a[$llave] += $b[$llave];
      } else {
        throw new \RuntimeException('Existe un problema para operar en la llave ' . $llave . '.');
        break;
      }
    }

    return $a;
  }

  /**
   * Dado un índice asociativo y un arreglo, devuelve el índice numérico correspondiente al asociativo
   *
   * @param string $index: Índice asociativo del arreglo
   * @param array $a: Arreglo a evaluar
   *
   * @return int el índice correspondiente, -1 si no existe el indice
   */
  public static function get_key_by_index(string $index, array $a) : int {
    $i = 0;
    foreach ($a as $key => $val) {
      if ($key == $index) {
        return (int) $i;
      }
      $i++;
    }
    return -1;
  }

  /**
   * Elimina todos los elementos repetidos de un array
   * (string) '1' se considera igual a (int) 1
   *
   * @param array $a: Arreglo a evaluar
   *
   * @return array devuelve un arreglo sin elementos repetidos
   * http://stackoverflow.com/questions/8321620/array-unique-vs-array-flip
   */
  public static function unique_array(array $a) : array {
    return array_keys(array_flip($a));
  }

  /**
    * Evalúa si un arreglo es de tipo asociativo o no
    *
    * @param array $a: Arreglo a evaluar
    *
    * @return bool false si no lo es, true si lo es
  */
  public static function is_assoc(array $a) : bool {
    if (sizeof($a) == 0) {
      return false;
    }

    return (bool) (array_keys($a) !== range(0, count($a) - 1));
  }

  /**
    * Evalúa si un arreglo es secuencial (de índices numéricos)
    *
    * @param array $a: Arreglo a evaluar
    *
    * @return bool false si no lo es, true si lo es
  */
  public static function is_numeric_array(array $a) : bool {
    return !self::is_assoc($a);
  }

  /**
    * Obtiene de forma random un elemento de un arreglo
    *
    * @param array $a: Arreglo a evaluar
    *
    * @return mixed elemento random dentro del arreglo
  */
  public static function array_random_element(array $a) {
    return $a[array_rand($a)];
  }

  /**
    * Ordena una matriz de mayor a menor, o menor a mayor por el valor de un campo específico
    *
    * @param array $toOrderArray : Arreglo a evaluar
    * @param string $field: Campo por el que se va a ordenar
    * @param bool $inverse: Por defecto falso, cambia el orden
    *
    * @return mixed elemento random dentro del arreglo
  */
  public static function order_multi_dimensional(array $toOrderArray, string $field, bool $inverse = false) : array {
    $position = array();
    $newRow = array();
    foreach ($toOrderArray as $key => $row) {
      $position[$key]  = $row[$field];
      $newRow[$key] = $row;
    }
    $inverse ? arsort($position) : asort($position);
    $returnArray = array();

    foreach ($position as $key => $pos) {     
      $returnArray[] = $newRow[$key];
    }

    return $returnArray;
  }

  /**
   * Se obtiene de Twig_Extension y sirve para que cada función esté disponible como etiqueta en twig
   *
   * @return array con todas las funciones con sus respectivos nombres de acceso en plantillas twig
   */
  public function getFunctions() : array {
    return array(
      new \Twig_Function('get_key_by_index', array($this, 'get_key_by_index')),
      new \Twig_Function('unique_array', array($this, 'unique_array')),
      new \Twig_Function('is_assoc', array($this, 'is_assoc')),
      new \Twig_Function('is_numeric_array', array($this, 'is_numeric_array')),
      new \Twig_Function('array_random_element', array($this, 'array_random_element')),
      new \Twig_Function('arrays_sum', array($this, 'arrays_sum'))
    );
  }

  /**
   * Identificador único para la extensión de twig
   *
   * @return string con el nombre de la extensión
   */
  public function getName() : string {
    return 'ocrend_framework_helper_arrays';
  }

}