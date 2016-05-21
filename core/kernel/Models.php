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

    $this->id = $_GET['id'] ?? null;
    $this->id = intval($this->id);
  }

  protected function __destruct() {
    $this->db->close();
  }

}

?>
