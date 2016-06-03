<?php

class Conexion extends mysqli {

  public function __construct($DATABASE = DB_NAME) {
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

  final public function delete(string $table, string $where, string $limit = "") {
    $this->query("DELETE FROM $table WHERE $where $limit;");
  }

}

?>
