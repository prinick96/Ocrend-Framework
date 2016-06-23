<?php

defined('INDEX_DIR') OR exit('Ocrend software says .i.');

abstract class Controllers {

  protected $template;
  protected $isset_id;
  protected $method;
  protected $route;

  /**
    * Cargador de funciones para Plates
    *
    * @return void
  */
  private function loadFunctions() {

    $this->template->registerFunction('url_amigable', function ($var) {
      return Func::url_amigable($var);
    });

  }

  /**
    * Constructor, inicializa los alcances de todos los Controladores
    *
    * @param bool $LOGED: Si el controlador en cuestión será solamente para usuarios logeados, se pasa TRUE
    * @param bool $CACHE: Si la VISTA para el controlador en cuestión se quiere compilar una única sin detectar futuros cambios vez se pasa TRUE
    *
    * @return void
  */
  protected function __construct(bool $LOGED = false) {

    global $router;

    #Accedemos a el router para URL's amigables
    $this->route = $router;

    #Restricción para usuarios logeados
    if($LOGED and !isset($_SESSION[SESS_APP_ID])) {
      Func::redir();
      exit;
    }

    #Carga del template
    $this->template = new League\Plates\Engine('templates','phtml');
    $this->loadFunctions();

    #Debug mode
    if(DEBUG) {
      $_SESSION['___QUERY_DEBUG___'] = array();
    }

    #Utilidades
    $this->method = ($router->getMethod() != null and Func::alphanumeric($router->getMethod())) ? $router->getMethod() : null;
    $this->isset_id = ($router->getId() != null and is_numeric($router->getId()) and $router->getId() >= 1);

  }

}

?>
