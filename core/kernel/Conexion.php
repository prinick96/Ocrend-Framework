<?php

defined('INDEX_DIR') OR exit('Ocrend software says .i.');

final class Conexion extends PDO {

  private static $inst;

  /**
    * Inicia la instancia de conexión, si esta ya ha sido declarada antes, no la duplica y ahorra memoria.
    *
    * @param string $DATABASE, se pasa de forma opcional una base de datos distinta a la definida en DATABASE['name'] para conectar
    *
    * @return la instancia de conexión
  */
  final public static function Start($DATABASE = DATABASE['name'], $MOTOR = DATABASE['motor']) {

    if(!self::$inst instanceof self) {
      self::$inst = new self($DATABASE,$MOTOR);
    }

    return self::$inst;
  }

  /**
    * Inicia la conexión con una base de datos
    *
    * @param string $DATABASE, se pasa de forma opcional una base de datos distinta a la definida en DATABASE['name'] para conectar
    *
    * @return void
  */
  final public function __construct($DATABASE = DATABASE['name'], $MOTOR = DATABASE['motor']) {
    try {
      $host = $MOTOR.':host='.DATABASE['host'].';dbname='.$DATABASE;
      parent::__construct($host,'root','',array(
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
      ));
    } catch (PDOException $e) {
      die('Error intentando conectar con la base de datos.');
    } finally {
      unset($host);
    }

  }

  /**
    * Consigue el numero de filas encontradas después de un SELECT
    *
    * @param object PDOStatement $query, valor devuelto de la query
    *
    * @return numero de filas encontradas
  */
  final public function rows($query) : int {
    return $query->rowCount();
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

    $q = $this->quote($e);
    $q[0] = '';
    $q[strlen($q) - 1] = '';

    return trim($q);
  }

  /**
    * Realiza una query, y si está en modo debug analiza que query fue ejecutada y el peso de esta en memoria
    *
    * @param SQL string, recibe la consulta SQL a ejecutar
    *
    * @return object PDOStatement
  */
  final public function query(string $q) {
    try {

      if(DEBUG) {
        $_SESSION['___QUERY_DEBUG___'][] = (string) $q;
      }

      return parent::query($q);
    } catch (Exception $e) {
      die('Error en la query: <b>' . $q . '<b/><br /><br />' . $e->getMessage());
    }
  }

  /**
    * Borra una serie de elementos de forma segura de una tabla en la base de datos
    *
    * @param string $table: Tabla a la cual se le quiere remover un elemento
    * @param string $where: Condición de borrado que define quien/quienes son dichos elementos
    * @param string $limit: Por defecto se limita a borrar un solo elemento que cumpla el $where
    *
    * @return object PDOStatement
  */
  final public function delete(string $table, string $where, string $limit = 'LIMIT 1')  {
    return $this->query("DELETE FROM $table WHERE $where $limit;");
  }

  /**
    * Inserta una serie de elementos a una tabla en la base de datos
    *
    * @param string $table: Tabla a la cual se le va a insertar elementos
    * @param array $e: Arreglo asociativo de elementos, con la estrctura 'campo_en_la_tabla' => 'valor_a_insertar_en_ese_campo',
    *                  todos los elementos del arreglo $e, serán sanados por el método sin necesidad de hacerlo manualmente al crear el arreglo
    *
    * @return object PDOStatement
  */
  final public function insert(string $table, array $e) {
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
    * @return object PDOStatement
  */
  final public function update(string $table, array $e, string $where, string $limit = 'LIMIT 1') {
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
      foreach ($sql as $d) {
        $s[] = $d;
      }
    } else {
      $s = false;
    }

    return $s;
  }

  /**
    * Alert para evitar clonaciones
    *
    * @return void
  */
  final public function __clone() {
    trigger_error('Estás intentando clonar la Conexión', E_USER_ERROR);
  }

}

?>
