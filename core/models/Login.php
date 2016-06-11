<?php

final class Login extends Models implements OCREND {

  private $pass;

  public function __construct() {
    parent::__construct();
  }

  final public function SignIn(string $user, string $pass) : array {

    $this->pass = $pass;

    $u = $this->db->select('id,pass','users',"user='$user'",'LIMIT 1');
    if(false != $u and Func::chash($u[0][1],$this->pass)) {
      $_SESSION['app_id'] = $u[0][0];
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
