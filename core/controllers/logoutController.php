<?php

class logoutController extends Controllers {

  public function __construct() {
    parent::__construct();
    unset($_SESSION[SESS_APP_ID]);
    session_write_close();
    session_unset();
    Func::redir();
  }

}

?>
