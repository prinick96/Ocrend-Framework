<?php

final class Register extends Models implements OCREND {

  private $user;
  private $email;

  public function __construct() {
    parent::__construct();
  }

  final public function SignUp(array $data) : array {

    if($this->AllFull($data)) {

      $this->user = $data['user'];
      $this->email = $data['email'];

      if(filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
        $u = $this->db->select('user','users',"user='$this->user' OR email='$this->email'",'LIMIT 1');
        if(false == $u) {
          $e = array(
            'user' => $data['user'],
            'pass' => Func::hash($data['pass']),
            'email' => $data['email'],
            'dni' => $data['dni'],
            'nombre' => $data['nombre'],
            'pais' => $data['pais'],
            'nacimiento' => $data['nacimiento'],
            'telefono' => str_replace([',','+','.',' '],'',$data['telefono'])
          );
          $this->db->insert('users',$e);
          $_SESSION[SESS_APP_ID] = $this->db->insert_id;
          $success = 1;
          $message = 'Registro completado con éxito, le estamos redireccionando.';
        } else {
          $success = 0;
          if(strtolower($u[0][0]) == strtolower($this->user)) {
            $message = 'El nombre de usuario ya existe.';
          } else {
            $message = 'El email utilizado ya existe.';
          }
        }
      } else {
        $success = 0;
        $message = 'La dirección <b>' . $this->email .'</b> no tiene un formato válido.';
      }
    } else {
      $success = 0;
      $message = 'Todos los campos deben estar llenos.';
    }

    return array('success' => $success, 'message' => $message);
  }

  public function __destruct() {
    parent::__destruct();
  }

}

?>
