<?php

class homeController extends Controllers {
  
  public function __construct() {
    parent::__construct();
    echo $this->template->render('home.twig');
  }

}

?>
