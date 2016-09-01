<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

final class Login extends Models implements OCREND {

  private $user;
  private $session = null;
  private $u;

  public function __construct() {
    parent::__construct();
  }

  # Control de errores
  final private function errors(array $data) {
    try {
      Helper::load('strings');
      $this->user = $this->db->scape($data['user']);
      $this->u = $this->db->select('id,pass','users',"user='$this->user'",'LIMIT 1');

      if(false == $this->u or !Strings::chash($this->u[0][1],$data['pass'])) {
        throw new Exception('<b>Error:</b> Credenciales incorrectas.');
      }

      if(DB_SESSION) {
        $this->session = new Sessions;
        if($this->session->session_in_use($this->u[0][0])) {
          throw new Exception('<b>Error:</b> Ya tiene una sesión iniciada.');
        }
      }

      return false;
    } catch (Exception $e) {
      return array('success' => 0, 'message' => $e->getMessage());
    }
  }

  # Inicio de sesión
  final public function SignIn(array $data) : array {
    $error = $this->errors($data);
    if(!is_bool($error)) {
      return $error;
    }

    if(DB_SESSION) {
      $this->session->generate_session($this->u[0][0]);
    } else {
      $_SESSION[SESS_APP_ID] = $this->u[0][0];
    }

    return array('success' => 1, 'message' => '<b>Conectado:</b> estamos redireccionando.');
  }

  public function __destruct() {
    parent::__destruct();
  }

}

?>
