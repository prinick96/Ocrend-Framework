<?php

final class Register extends Models implements OCREND {

  private $user;
  private $email;
  private $pass;
  private $dni;
  private $nombre;
  private $pais;
  private $nacimiento;
  private $telefono;

  public function __construct() {
    parent::__construct();
  }

  final public function SignUp(array $data) : array {

    if($this->AllFull($data)) {
      $this->user = $this->db->scape($data['user']);
      $this->email = $this->db->scape($data['email']);

      if(filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
        $sql = $this->db->query("SELECT user FROM users WHERE user='$this->user' OR email='$this->email' LIMIT 1;");
        if($this->db->rows($sql) == 0) {
          $this->pass = Func::hash($data['pass']);
          $this->dni = $this->db->scape($data['dni']);
          $this->nombre = $this->db->scape($data['nombre']);
          $this->pais = $this->db->scape($data['pais']);
          $this->nacimiento = $this->db->scape($data['nacimiento']);
          $this->telefono = $this->db->scape(str_replace([',','+','.',' '],'',$data['telefono']));

          $this->db->query("INSERT INTO users (user,email,pass,dni,nombre,pais,nacimiento,telefono)
          VALUES ('$this->user','$this->email','$this->pass','$this->dni','$this->nombre','$this->pais',
          '$this->nacimiento','$this->telefono');");

          $_SESSION['app_id'] = $this->db->insert_id;
          $success = 1;
          $message = 'Registro completado con éxito, le estamos redireccionando.';
        } else {
          $success = 0;
          if(strtolower($this->db->recorrer($sql)[0]) == strtolower($this->user)) {
            $message = 'El nombre de usuario ya existe.';
          } else {
            $message = 'El email utilizado ya existe.';
          }
        }
        $this->db->liberar($sql);
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
