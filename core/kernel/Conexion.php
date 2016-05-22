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
    return $query->fetch_array(MYSQLI_NUM);
  }

  final public function assoc($query) {
    return $query->fetch_assoc();
  }

}

?>
