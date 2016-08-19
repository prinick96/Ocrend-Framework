<?php

final class Register extends Models implements OCREND {

  public function __construct() {
    parent::__construct();
  }

  # Control de errores
  final private function errors(array $data) {
    try {

      if(isset($_SESSION['pe_time']) and end($_SESSION['pe_time']) >= time()) {
        throw new Exception('No puedes realizar tantas acciones seguidas.');
      }

      if(!Func::all_full($data)) {
        throw new Exception('<b>Error:</b> Todos los campos son necesarios.');
      }

      $user = $this->db->scape($data['user']);
      $email = $this->db->scape($data['email']);

      Helper::load('strings');

      if(!Strings::is_email($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('<b>Error:</b> Email no tiene un formato válido.');
      }

      $u = $this->db->select('user','users',"user='$user' OR email='$email'",'LIMIT 1');
      if(false != $u) {
        if(strtolower($u[0][0]) == strtolower($user)) {
          throw new Exception('<b>Error:</b> El usuario ya existe.');
        } else {
          throw new Exception('<b>Error:</b> El email ya existe.');
        }
      }

      return false;
    } catch (Exception $e) {
      return array('success' => 0, 'message' => $e->getMessage());
    }
  }

  # Registro
  final public function SignUp(array $data) : array {
    $error = $this->errors($data);
    if(!is_bool($error)) {
      return $error;
    }

    $e = array(
      'user' => $data['user'],
      'pass' => Strings::hash($data['pass']),
      'email' => $data['email'],
      'dni' => $data['dni'],
      'nombre' => $data['nombre'],
      'pais' => $data['pais'],
      'session' => DB_SESSION ? (time() + SESSION_TIME) : 0,
      'nacimiento' => $data['nacimiento'],
      'telefono' => str_replace([',','+','.',' '],'',$data['telefono'])
    );
    $this->db->insert('users',$e);

    # Generamos la sesión
    $_SESSION[SESS_APP_ID] = $this->db->lastInsertId();

    # Seguridad
    $_SESSION['pe_time'][] = time() + 5;

    return array('success' => 1, 'message' => 'Registro completado con éxito, le estamos redireccionando.');
  }

  public function __destruct() {
    parent::__destruct();
  }

}

?>
