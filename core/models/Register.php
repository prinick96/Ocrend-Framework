<?php

final class Register extends Models implements OCREND {

  private $user;
  private $email;

  public function __construct() {
    parent::__construct();
  }


  final public function SignUp(array $data) : array {

    try {

      if(isset($_SESSION['pe_time']) and end($_SESSION['pe_time']) >= time()) {
        throw new Exception('No puedes realizar tantas acciones seguidas.');
      }

      if(Func::all_full($data)) {

        $this->user = $this->db->scape($data['user']);
        $this->email = $this->db->scape($data['email']);

        Helper::load('strings');

        if(Strings::is_email($this->email, FILTER_VALIDATE_EMAIL)) {
          $u = $this->db->select('user','users',"user='$this->user' OR email='$this->email'",'LIMIT 1');
          if(false == $u) {
            $e = array(
              'user' => $data['user'],
              'pass' => Strings::hash($data['pass']),
              'email' => $data['email'],
              'dni' => $data['dni'],
              'nombre' => $data['nombre'],
              'pais' => $data['pais'],
              'nacimiento' => $data['nacimiento'],
              'telefono' => str_replace([',','+','.',' '],'',$data['telefono'])
            );
            $this->db->insert('users',$e);
            $_SESSION[SESS_APP_ID] = $this->db->lastInsertId();
            $success = 1;
            $message = 'Registro completado con éxito, le estamos redireccionando.';

            # Seguridad
            $_SESSION['pe_time'][] = time() + 5;

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
    } catch (Exception $e) {

      $success = 0;
      $message = $e->getMessage();
    } finally {

      return array('success' => $success, 'message' => $message);
    }
  }

  public function __destruct() {
    parent::__destruct();
  }

}

?>
