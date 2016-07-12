<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

/**
  * Interfaz implementada para TODOS los modelos sin excepción, que requieran interacción alguna con la base de datos
*/
interface OCREND {
  public function __construct();
  public function __destruct();
}

//------------------------------------------------

abstract class Models {

  //------------------------------------------------

  protected $db;
  protected $id;
  protected $id_user;

  //------------------------------------------------

  /**
    * Constructor, inicializa los alcances de todos los Modelos
    *
    * @param string $DATABASE, se pasa de forma opcional una base de datos distinta a la definida en DATABASE['name'] para conectar
    *
    * @return void
  */
  protected function __construct($DATABASE = DATABASE['name'], $MOTOR = DATABASE['motor'], $new_instance = false) {

    global $router;

    $this->db = Conexion::Start($DATABASE,$MOTOR,$new_instance);
    $this->id = ($router->getId() != null and is_numeric($router->getId()) and $router->getId() >= 1) ? (int) $router->getId() : 0;
    $this->id_user = $_SESSION[SESS_APP_ID] ?? 0;
  }

  //------------------------------------------------

  /**
    * Destructor, finaliza cualquier modelo y cierra la conexión inicializada en el constructor
    *
    * @return void
  */
  protected function __destruct() {
    $this->db = null;
  }

}

?>
