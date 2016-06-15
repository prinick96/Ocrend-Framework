<?php

defined('INDEX_DIR') OR exit('Ocrend software says .i.');

#INTERFACE implementada EN TODOS los modelos SIN EXCEPCIÓN
interface OCREND {
  public function __construct();
  public function __destruct();
}

abstract class Models {

  protected $db;
  protected $id;
  protected $id_user;

  protected function __construct($DATABASE = DB_NAME) {
    $this->db = Conexion::Start($DATABASE);
    $this->id = isset($_GET['id']) ? intval($_GET['id']) : null;
    $this->id_user = $_SESSION['app_id'] ?? 0;
  }

  #Analiza todo un arreglo en busca de posibles elementos vacíos, si todos están llenos devuelve true
  protected function AllFull(array $array) : bool {
    foreach($array as $e) {
      if(empty($e) and $e != '0') {
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
