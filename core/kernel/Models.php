<?php

#INTERFACE implementada EN TODOS los modelos SIN EXCEPCIÓN
interface OCREND {
  public function __construct();
  public function __destruct();
}

abstract class Models {

  protected $db;
  protected $id;

  protected function __construct() {
    $this->db = new Conexion();
    $this->id = intval($_GET['id']) ?? null;
  }

  protected function __destruct() {
    $this->db->close();
  }

}

?>
