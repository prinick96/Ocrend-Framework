<?php

defined('INDEX_DIR') OR exit('Ocrend software says .i.');

final class Conexion extends mysqli {

  private static $inst;

  /**
    * Inicia la instancia de conexión, si esta ya ha sido declarada antes, no la duplica y ahorra memoria.
    *
    * @param string $DATABASE, se pasa de forma opcional una base de datos distinta a la definida en DB_NAME para conectar
    *
    * @return la instancia de conexión
  */
  final public static function Start($DATABASE = DB_NAME) {

    if(!self::$inst instanceof self) {
      self::$inst = new self($DATABASE);
    }

    return self::$inst;
  }

  /**
    * Inicia la conexión con una base de datos
    *
    * @param string $DATABASE, se pasa de forma opcional una base de datos distinta a la definida en DB_NAME para conectar
    *
    * @return void
  */
  final public function __construct($DATABASE = DB_NAME) {
    parent::__construct(DB_HOST,DB_USER,DB_PASS,$DATABASE);
    $this->connect_errno ? die('Error al conectar con la base de datos') : null;
    $this->set_charset('utf8');
  }

  /**
    * Libera el buffer para una query SELECT
    *
    * @param object mysqli_result $query, valor devuelto de la query
    *
    * @return void
  */
  final public function liberar($query) {
    return $query->free();
  }

  /**
    * Consigue el numero de filas encontradas después de un SELECT
    *
    * @param object mysqli_result $query, valor devuelto de la query
    *
    * @return numero de filas encontradas
  */
  final public function rows($query) : int {
    return $query->num_rows;
  }

  /**
    * Convierte el object mysqli_result en un arreglo asociativo/numérico con cada campo después de un SELECT
    *
    * @param object mysqli_result $query, valor devuelto de la query
    *
    * @return array con información de cada campo obtenido, en orden 'campo' => valor, false cuando no consigue más
  */
  final public function recorrer($query) {
    return $query->fetch_array();
  }

  /**
    * Convierte el object mysqli_result en un arreglo estrictamente asociativo con cada campo después de un SELECT
    *
    * @param object mysqli_result $query, valor devuelto de la query
    *
    * @return array con información de cada campo obtenido, en orden 'campo' => valor, false cuando no consigue más
  */
  final public function assoc($query) {
    return $query->fetch_assoc();
  }

  /**
    * Sana un valor para posteriormente ser introducido en una query
    *
    * @param string/int/float a sanar
    *
    * @return int/float/string sanados según sea el tipo de dato pasado por parámetro
  */
  final public function scape($e) {
    if(is_int($e)) {
      return intval($e);
    } else if (is_float($e)) {
      return floatval($e);
    }
    return $this->real_escape_string($e);
  }

  /**
    * Realiza una query, y si está en modo debug analiza que query fue ejecutada y el peso de esta en memoria
    *
    * @param SQL string, recibe la consulta SQL a ejecutar
    *
    * @return object mysqli_result
  */
  final public function query($q) {

    if(DEBUG) {
      $i = (int) memory_get_usage();
      $query = parent::query($q);
      $f = (int) memory_get_usage();

      $_SESSION['___QUERY_DEBUG___'][] = [
        (string) $q,
        (int) $f - $f
      ];

      unset($i,$f,$q);
      return $query;
    }

    return parent::query($q);
  }

  /**
    * Borra una serie de elementos de forma segura de una tabla en la base de datos
    *
    * @param string $table: Tabla a la cual se le quiere remover un elemento
    * @param string $where: Condición de borrado que define quien/quienes son dichos elementos
    * @param string $limit: Por defecto se limita a borrar un solo elemento que cumpla el $where
    *
    * @return true si fue ejecutado con éxito, false si no fue ejecutado con éxito
  */
  final public function delete(string $table, string $where, string $limit = 'LIMIT 1') : bool {
    return $this->query("DELETE FROM $table WHERE $where $limit;");
  }

  /**
    * Inserta una serie de elementos a una tabla en la base de datos
    *
    * @param string $table: Tabla a la cual se le va a insertar elementos
    * @param array $e: Arreglo asociativo de elementos, con la estrctura 'campo_en_la_tabla' => 'valor_a_insertar_en_ese_campo',
    *                  todos los elementos del arreglo $e, serán sanados por el método sin necesidad de hacerlo manualmente al crear el arreglo
    *
    * @return true si fue ejecutado con éxito, false si no fue ejecutado con éxito
  */
  final public function insert(string $table, array $e) : bool {
    if (sizeof($e) == 0) {
      trigger_error('El arreglo pasado por $this->db->insert(...) está vacío.', E_USER_ERROR);

      return false;
    }

    $query = "INSERT INTO $table (";
    $values = '';
    foreach ($e as $campo => $v) {
      $query .= $campo . ',';
      $values .= '\'' . $this->scape($v) . '\',';
    }
    $query[strlen($query) - 1] = ')';
    $values[strlen($values) - 1] = ')';
    $query .= ' VALUES (' . $values . ';';

    return $this->query($query);
  }

  /**
    * Actualiza elementos de una tabla en la base de datos según una condición
    *
    * @param string $table: Tabla a actualizar
    * @param array $e: Arreglo asociativo de elementos, con la estrctura 'campo_en_la_tabla' => 'valor_a_insertar_en_ese_campo',
    *                  todos los elementos del arreglo $e, serán sanados por el método sin necesidad de hacerlo manualmente al crear el arreglo
    * @param string $where: Condición que indica quienes serán modificados
    * @param string $limite: Límite de elementos modificados, por defecto solo modifica el primero que cumpla la condición
    *
    * @return true si fue ejecutado con éxito, false si no fue ejecutado con éxito
  */
  final public function update(string $table, array $e, string $where, string $limit = 'LIMIT 1') : bool {
    if (sizeof($e) == 0) {
      trigger_error('El arreglo pasado por $this->db->update(...) está vacío.', E_USER_ERROR);

      return false;
    }

    $query = "UPDATE $table SET ";
    foreach ($e as $campo => $valor) {
      $query .= $campo . '=\'' . $this->scape($valor) . '\',';
    }
    $query[strlen($query) - 1] = ' ';
    $query .= "WHERE $where $limit;";

    return $this->query($query);
  }

  /**
    * Selecciona y lista en un arreglo asociativo/numérico los resultados de una búsqueda en la base de datos
    *
    * @param string $e: Elementos a seleccionar separados por coma
    * @param string $tbale: Tabla de la cual se quiere extraer los elementos $e
    * @param string $where: Condición que indica quienes son los que se extraen, si no se coloca extrae todos
    * @param string $limite: Límite de elemntos a traer, por defecto trae TODOS los que cumplan $where
    *
    * @return false si no encuentra ningún resultado, array asociativo/numérico si consigue al menos uno
  */
  final public function select(string $e, string $table, string $where = '1 = 1', string $limit = "") {
    $sql = $this->query("SELECT $e FROM $table WHERE $where $limit;");
    if($this->rows($sql) > 0) {
      while ($d = $this->recorrer($sql)) {
        $s[] = $d;
      }
    } else {
      $s = false;
    }
    $this->liberar($sql);

    return $s;
  }

  /**
    * Cierra la conexión actual
    *
    * @return void
  */
  final public function close() {
    if(!self::$inst instanceof self) {
      return parent::close();
    }
  }

  /**
    * Alert para evitar clonaciones
    *
    * @return void
  */
  final public function __clone() {
    trigger_error('Estás intentando clonar la Conexión', E_USER_ERROR);
  }

  /**
    * Alert para evitar deserializaciones
    *
    * @return void
  */
  final public function __wakeup() {
    trigger_error('Estás intentando deserializar la Conexión', E_USER_ERROR);
  }

}

?>
