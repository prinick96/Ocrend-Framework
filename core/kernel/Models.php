<?php

defined('INDEX_DIR') OR exit('Ocrend software says .i.');

/**
  * Interfaz implementada para TODOS los modelos sin excepción, que requieran interacción alguna con la base de datos
*/
interface OCREND {
  public function __construct();
  public function __destruct();
}

abstract class Models {

  protected $db;
  protected $id;
  protected $id_user;

  /**
    * Constructor, inicializa los alcances de todos los Modelos
    *
    * @param string $DATABASE, se pasa de forma opcional una base de datos distinta a la definida en DB_NAME para conectar
    *
    * @return void
  */
  protected function __construct($DATABASE = DB_NAME) {
    $this->db = Conexion::Start($DATABASE);
    $this->id = isset($_GET['id']) ? intval($_GET['id']) : null;
    $this->id_user = $_SESSION[SESS_APP_ID] ?? 0;
  }

  /**
    * Aanaliza que TODOS los elementos de un arreglo estén llenos, útil para analizar por ejemplo que todos los elementos de un formulario esté llenos
    * pasando como parámetro $_POST
    *
    * @param array $array, arreglo a analizar
    *
    * @return true si están todos llenos, false si al menos uno está vacío
  */
  protected function AllFull(array $array) : bool {
    foreach($array as $e) {
      if(empty($e) and $e != '0') {
        return false;
      }
    }
    return true;
  }

  /**
    * Destructor, finaliza cualquier modelo y cierra la conexión inicializada en el constructor
    *
    * @return void
  */
  protected function __destruct() {
    $this->db->close();
  }

}

?>
