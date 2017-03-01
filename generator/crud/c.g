<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

class {{controller}} extends Controllers {

  public function __construct() {
    parent::__construct();

    ${{variable}} = new {{model}};

    switch($this->method) {
      case 'crear':
        echo $this->template->render('{{view}}/crear');
      break;
      case 'editar':
        if($this->isset_id and false !== ($item = ${{variable}}->leer(false))) {
          echo $this->template->render('{{view}}/editar', array(
            'data' => $item[0]
          ));
        } else {
          Func::redir(URL . '{{view}}/');
        }
      break;
      case 'eliminar':
        ${{variable}}->borrar();
      break;
      default:
        echo $this->template->render('{{view}}/{{view}}',array(
          'data' => ${{variable}}->leer()
        ));
      break;
    }
  }

}

?>
