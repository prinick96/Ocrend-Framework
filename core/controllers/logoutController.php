<?php

class logoutController extends Controllers {

  public function __construct() {
    parent::__construct();
    unset($_SESSION['app_id']);
    session_write_close();
    session_unset();
    Func::redir();
  }

}

?>
