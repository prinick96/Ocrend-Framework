<?php

class {{controller}} extends Controllers {

  public function __construct() {
    parent::__construct();
    echo $this->template->render('{{view}}/{{view}}');
  }

}

?>
