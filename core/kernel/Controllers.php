<?php

defined('INDEX_DIR') OR exit('Ocrend software says .i.');

abstract class Controllers {

  protected $template;
  protected $isset_id;
  protected $method;
  protected $route;

  /**
    * Constructor, inicializa los alcances de todos los Controladores
    *
    * @param bool $LOGED: Si el controlador en cuestión será solamente para usuarios logeados, se pasa TRUE
    * @param bool $CACHE: Si la VISTA para el controlador en cuestión se quiere compilar una única sin detectar futuros cambios vez se pasa TRUE
    *
    * @return void
  */
  protected function __construct(bool $LOGED = false, bool $CACHE = false) {

    global $router;

    #Accedemos a el router para URL's amigables
    $this->route = $router;

    #Debug mode
    if(DEBUG) {
      $_SESSION['___QUERY_DEBUG___'] = array();
    }

    #Restricción para usuarios logeados
    if($LOGED and !isset($_SESSION[SESS_APP_ID])) {
      Func::redir();
      exit;
    }

    #Definición de templates
    $this->template = new Twig_Environment(new Twig_Loader_Filesystem('templates'), array(
      'cache' => 'templates/.compiler',
      'auto_reload' => !$CACHE
    ));

    #Definición de globales disponibles en templates
    $this->template->addGlobal('controller', str_replace('Controller','',$router->getController()));
    $this->template->addGlobal('session', $_SESSION);
    $this->template->addGlobal('get', $_GET);
    $this->template->addGlobal('post', $_POST);

    #Añadimos la función para convertir texto común en url amigable
    $url_amigable = new Twig_SimpleFunction('url_amigable',function(string $s) {
      return Func::url_amigable($s);
    });
    $this->template->addFunction($url_amigable);

    /*
      #AGREGAR FUNCIÓN A TWIG
      $function = new Twig_SimpleFunction('MiFuncionDesdeTwig',function($parametros) {
        return MiFuncion($parametros);
      });
      $this->template->addFunction($function);
    */

    #Utilidades
    $this->method = ($router->getMethod() != null and Func::alphanumeric($router->getMethod())) ? $router->getMethod() : null;
    $this->isset_id = ($router->getId() != null and is_numeric($router->getId()) and $router->getId() >= 1);

  }

}

?>
