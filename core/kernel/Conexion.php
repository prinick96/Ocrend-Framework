<?php

class Conexion extends mysqli {

  public function __construct($DATABASE = DB_NAME) {
    parent::__construct(DB_HOST,DB_USER,DB_PASS,$DATABASE);
    $this->connect_errno ? die('Error al conectar con la base de datos') : null;
    $this->set_charset('utf8');

    unset($_SESSION['dbug_querys']);
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

  final public function query($query) {
    if(DEBUG) {
      $q = parent::query($query);
      $_SESSION['dbug_querys'][] = $q;
      return $q;
    }
    return parent::query($query);
  }

  final public function delete(string $table, string $where, string $limit = 'LIMIT 1') {
    $this->query("DELETE FROM $table WHERE $where $limit;");
  }

  final public function scape($e) {
    if(is_int($e)) {
      return intval($e);
    } else if (is_float($e)) {
      return floatval($e);
    }
    return $this->real_escape_string($e);
  }

  final public function close() {
    unset($_SESSION['dbug_querys']);
    return parent::close();
  }

}

?>
