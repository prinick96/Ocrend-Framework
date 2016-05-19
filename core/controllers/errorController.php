<?php

class errorController extends Controllers {

  public function __construct() {
    parent::__construct(true);
    echo $this->template->render('error.twig');
  }

}

?>
