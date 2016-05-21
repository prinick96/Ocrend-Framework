<?php

class Conexion extends mysqli {

  public function __construct($DATABASE = DB_NAME) {
    parent::__construct(DB_HOST,DB_USER,DB_PASS,$DATABASE);
    $this->connect_errno ? die('Error al conectar con la base de datos') : null;
    $this->set_charset('utf8');
  }

  final public function liberar($query) {
    return mysqli_free_result($query);
  }

  final public function rows($query) : int {
    return mysqli_num_rows($query);
  }

  final public function recorrer($query) {
    return mysqli_fetch_array($query);
  }

  final public function assoc($query) {
    return mysqli_fetch_assoc($query);
  }

}

?>
