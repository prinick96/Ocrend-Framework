<?php

class Conexion extends mysqli {

  public function __construct($DATABASE = DB_NAME) {
    parent::__construct(DB_HOST,DB_USER,DB_PASS,$DATABASE);
    $this->connect_errno ? die('Error al conectar con la base de datos') : null;
    $this->set_charset('utf8');
  }

  public function liberar($query) {
    return mysqli_free_result($query);
  }

  public function rows($query) : int {
    return mysqli_num_rows($query);
  }

  public function recorrer($query) {
    return mysqli_fetch_array($query);
  }

  public function assoc($query) {
    return mysqli_fetch_assoc($query);
  }

}

?>
