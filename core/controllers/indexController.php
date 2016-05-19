<?php

class indexController extends Controllers {

  public function __construct() {
    parent::__construct();
    echo $this->template->render('index.twig');
  }

}

?>
