<?php

final class Login extends Models implements OCREND {

  private $user;
  private $pass;

  public function __construct() {
    parent::__construct();
  }

  final public function SignIn(string $user, string $pass) : array {
    $this->user = $this->db->scape($user);
    $this->pass = md5(md5($pass) . 'ocrend');
    $sql = $this->db->query("SELECT id FROM users WHERE user='$this->user' AND pass='$this->pass' LIMIT 1;");
    if($this->db->rows($sql) > 0) {
      $_SESSION['app_id'] = $this->db->recorrer($sql)[0];
      $success = 1;
      $message = 'Conectado, estamos redireccionando.';
    } else {
      $success = 0;
      $message = 'Credenciales incorrectas.';
    }
    $this->db->liberar($sql);

    return array('success' => $success, 'message' => $message);
  }

  public function __destruct() {
    parent::__destruct();
  }

}

?>
