<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

class {{controller}} extends Controllers {

  public function __construct() {
    parent::__construct();
    echo $this->template->render('{{view}}/{{view}}');
  }

}

?>
