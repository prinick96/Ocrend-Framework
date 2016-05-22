<?php

#INTERFACE implementada EN TODOS los modelos SIN EXCEPCIÓN
interface OCREND {
  public function __construct();
  public function __destruct();
}

abstract class Models {

  protected $db;
  protected $id;

  protected function __construct($DATABASE = DB_NAME) {
    $this->db = new Conexion($DATABASE);
    $this->id = $_GET['id'] ?? null;
    $this->id = intval($this->id);
  }

  #Analiza todo un arreglo en busca de posibles elementos vacíos, si todos están llenos devuelve true
  protected function AllFull(array $array) : bool {
    foreach($array as $e) {
      if(empty($e)) {
        return false;
      }
    }
    return true;
  }

  protected function __destruct() {
    $this->db->close();
  }

}

?>
