<?php

class errorController extends Controllers {

  public function __construct() {
    parent::__construct();
    echo $this->template->render('error.twig');
  }

}

?>
