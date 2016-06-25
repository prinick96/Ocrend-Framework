<?php

final class Login extends Models implements OCREND {

  private $user;

  public function __construct() {
    parent::__construct();
  }

  final public function SignIn(array $data) : array {

    Helper::load('strings');
    $this->user = $this->db->scape($data['user']);

    $u = $this->db->select('id,pass','users',"user='$this->user'",'LIMIT 1');
    if(false != $u and Strings::chash($u[0][1],$data['pass'])) {
      $_SESSION[SESS_APP_ID] = $u[0][0];
      $success = 1;
      $message = 'Conectado, estamos redireccionando.';
    } else {
      $success = 0;
      $message = 'Credenciales incorrectas.';
    }

    return array('success' => $success, 'message' => $message);
  }

  public function __destruct() {
    parent::__destruct();
  }

}

?>
