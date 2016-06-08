<?php

final class Login extends Models implements OCREND {

  private $user;
  private $pass;

  public function __construct() {
    parent::__construct();
  }

  final public function SignIn(string $user, string $pass) : array {
    $this->user = $this->db->scape($user);
    $this->pass = $pass;
    #Atención: este login imposibilita conexión para usuarios con el mismo user, por eso se pone la restricción previa en Reg
    $sql = $this->db->query("SELECT id,pass FROM users WHERE user='$this->user' LIMIT 1;");
    if($this->db->rows($sql) > 0) {
      $u = $this->db->recorrer($sql);
      if(Func::chash($u[1],$this->pass)) { #comparamos el hash dinámico con el estático de la base de datos
        $_SESSION['app_id'] = $u[0];
        $success = 1;
        $message = 'Conectado, estamos redireccionando.';
      } else {
        $success = 0;
        $message = 'Credenciales incorrectas.';
      }
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
