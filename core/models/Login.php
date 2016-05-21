<?php

final class Login extends Models implements OCREND {

  private $user;
  private $pass;

  public function __construct() {
    parent::__construct();
  }

  final public function SignIn(string $user, string $pass) : array {
    $this->user = $this->db->real_escape_string($user);
    $this->pass = md5(md5($pass) . 'ocrend');
    $sql = $this->db->query("SELECT id FROM users WHERE user='$this->user' AND pass='$this->pass' LIMIT 1;");
    if($this->db->rows($sql) > 0) {
      $_SESSION['app_id'] = $this->db->recorrer($sql)[0];
      $success = 1;
    } else {
      $success = 0;
    }

    return array('success' => $success);
  }

  public function __destruct() {
    parent::__destruct();
  }

}

?>
