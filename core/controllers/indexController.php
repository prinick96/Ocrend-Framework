<?php

class indexController extends Controllers {

  public function __construct() {
    parent::__construct();
    exit;
    echo $this->template->render('index.twig');
  }

}

?>
