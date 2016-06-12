<?php

defined('INDEX_DIR') OR exit('Ocrend software says .i.');

final class Conexion extends mysqli {

  private static $inst;

  final public static function Start($DATABASE = DB_NAME) {
    if(!self::$inst instanceof self) {
      self::$inst = new self($DATABASE);
    }
    return self::$inst;
  }

  final public function __construct($DATABASE = DB_NAME) {
    parent::__construct(DB_HOST,DB_USER,DB_PASS,$DATABASE);
    $this->connect_errno ? die('Error al conectar con la base de datos') : null;
    $this->set_charset('utf8');
  }

  final public function liberar($query) {
    return $query->free();
  }

  final public function rows($query) : int {
    return $query->num_rows;
  }

  final public function recorrer($query) {
    return $query->fetch_array();
  }

  final public function assoc($query) {
    return $query->fetch_assoc();
  }

  final public function scape($e) {
    if(is_int($e)) {
      return intval($e);
    } else if (is_float($e)) {
      return floatval($e);
    }
    return $this->real_escape_string($e);
  }

  final public function delete(string $table, string $where, string $limit = 'LIMIT 1') : bool {
    return $this->query("DELETE FROM $table WHERE $where $limit;");
  }

  final public function insert(string $table, array $e) : bool {
    if (sizeof($e) == 0) {
      trigger_error('El arreglo pasado por $this->db->insert(...) está vacío.', E_USER_ERROR);

      return false;
    }

    $query = "INSERT INTO $table (";
    foreach ($e as $campo => $v) {
      $query .= $campo . ',';
    }
    $query[strlen($query) - 1] = ')';
    $query .= ' VALUES (';
    foreach ($e as $valor) {
      $query .= '\'' . $this->scape($valor) . '\',';
    }
    $query[strlen($query) - 1] = ')';

    return $this->query($query);
  }

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

  final public function close() {
    if(!self::$inst instanceof self) {
      return parent::close();
    }
  }

  final public function __clone() {
    trigger_error('Estás intentando clonar la Conexión', E_USER_ERROR);
  }

  final public function __wakeup() {
    trigger_error('Estás intentando deserializar la Conexión', E_USER_ERROR);
  }

}

?>
