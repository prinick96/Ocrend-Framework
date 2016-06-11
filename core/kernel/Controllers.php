<?php

defined('INDEX_DIR') OR exit('Ocrend software says .i.');

abstract class Controllers {

  protected $template;
  protected $isset_id;
  protected $mode;

  //Pasar TRUE en el constructor del controlador que será exlusivo para entidades LOGEADAS
  protected function __construct(bool $LOGED = false) {

    if($LOGED and !isset($_SESSION['app_id'])) {
      Func::redir('index.php');
      exit;
    }

    #Definición de templates
    $this->template = new Twig_Environment(new Twig_Loader_Filesystem('templates'), array(
      'cache' => 'templates/.compiler',
      'auto_reload' => true
    ));
    $this->template->addGlobal('session', $_SESSION);
    $this->template->addGlobal('get', $_GET);
    $this->template->addGlobal('post', $_POST);
    /*
      #AGREGAR FUNCIÓN A TWIG
      $function = new Twig_SimpleFunction('MiFuncionDesdeTwig',function($parametros) {
        return MiFuncion($parametros);
      });
      $this->template->addFunction($function);
    */

    $this->mode = $_GET['mode'] ?? null;
    $this->isset_id = isset($_GET['id']) and is_numeric($_GET['id']) and $_GET['id'] >= 1;
  }

}

?>
