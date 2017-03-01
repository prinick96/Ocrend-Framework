<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

abstract class Controllers {

  //------------------------------------------------

  protected $template;
  protected $isset_id;
  protected $method;
  protected $route;
  protected $session = null;

  //------------------------------------------------

  /**
    * Constructor, inicializa los alcances de todos los Controladores
    *
    * @param bool $LOGED: Si el controlador en cuestión será exclusivamente para usuarios logeados, se pasa TRUE
    * @param bool $UNLOGED: Si el controlador en cuestión será exclusivamente para usuarios NO logeados, se pasa TRUE
    *
    * @return void
  */
  protected function __construct(bool $LOGED = false, bool $UNLOGED = false) {

    global $router;

    # Accedemos a el router para URL's amigables
    $this->route = $router;

    # Control de vida de sesiones
    if(DB_SESSION) {
      $this->session = new Sessions;
      $this->session->check_life();
    }

    # Restricción para usuarios logeados
    if($LOGED and !isset($_SESSION[SESS_APP_ID])) {
      Func::redir(URL . 'logout/');
      exit;
    }

    # Restricción de página para ser visa sólamente por usuarios No logeados
    if($UNLOGED and isset($_SESSION[SESS_APP_ID])) {
      Func::redir();
      exit;
    }

    # TWIG ENGINE
    if(USE_TWIG_TEMPLATE_ENGINE) {
      # Más documentación http://gitnacho.github.io/Twig/
      $this->template = new Twig_Environment(new Twig_Loader_Filesystem('./templates/twig/'), array(
          # ruta donde se guardan los archivos compilados
          'cache' => './templates/twig/.cache/',
           # false para caché estricto, cero actualizaciones, recomendado para páginas 100% estáticas
          'auto_reload' => true,
          # en true, las plantillas generadas tienen un método __toString() para mostrar los nodos generados
          'debug' => DEBUG
      ));

      # Insertar por defecto, en TODOS los templates un elemento
      $this->template->addGlobal('session', $_SESSION);
      $this->template->addGlobal('get', $_GET);
      $this->template->addGlobal('post', $_POST);
      $this->template->addGlobal('route', $this->route);

      # Añadimos por defecto al modelo Func como extensión de twig
      $this->template->addExtension(new Func());
    }

    # PLATESPHP ENGINE
    else {
      # Carga del template
      $this->template = new League\Plates\Engine('templates/plates','phtml');

      /*
      # Insertar por defecto, en TODOS los templates un elemento o más
      $this->template->addData(array(
        'elemento' => $variable,
      ));
      */
    }

    # Debug
    if(DEBUG) {
      $_SESSION['___QUERY_DEBUG___'] = array();
    }

    # Utilidades
    $this->method = ($router->getMethod() != null and Strings::alphanumeric($router->getMethod())) ? $router->getMethod() : null;
    $this->isset_id = ($router->getId() != null and is_numeric($router->getId()) and $router->getId() >= 1);

  }

}

?>
