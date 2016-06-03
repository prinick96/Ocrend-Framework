<?php

/*
  Las funciones que requiren conexión con la base de datos o heredar algo del modelo, su previa llamda debe realizarse
  instanciando primero la clase Func, y llamarse a través de un objeto.

  Las que no, son static y utilizan Func::nombrefuncion()
*/

final class Func extends Models implements OCREND {

  public function __construct() {
    parent::__construct();
  }

  final public static function redir(string $url = 'index.php') {
    header('location: ' . $url);
  }

  final public static function send_mail(string $email, string $name, string $HTML, string $titulo) {
    $mail = new PHPMailer;
    $mail->CharSet = "UTF-8";
    $mail->Encoding = "quoted-printable";
    $mail->isSendMail();
    //$mail->isSMTP();
    $mail->Host = PHPMAILER_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = PHPMAILER_USER;
    $mail->Password = PHPMAILER_PASS;
    $mail->SMTPSecure = 'ssl';
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mail->Port = PHPMAILER_PORT;
    $mail->setFrom(PHPMAILER_USER, APP);
    $mail->addAddress($email, $name);
    $mail->isHTML(true);
    $mail->Subject = $titulo;
    $mail->Body    = $HTML;
    if(!$mail->send()) {
      return $mail->ErrorInfo;
    } else {
      return true;
    }
  }

  final public static function IsImage(string $file_name) : bool {
    $formats = ['jpg','png','jpeg','gif','JPG','PNG','JPEG','GIF'];
    $file_name = explode('.',$file_name);
    $ext = end($file_name);
    if(in_array($ext,$formats)){
      return true;
    }
    return false;
  }

  final public function CheckExists(int $id, string $table) {
    $sql = $this->db->query("SELECT * FROM $table WHERE id='$id' LIMIT 1;");
    if($this->db->rows($sql) > 0) {
      $exist = $this->db->recorrer($sql);
    } else {
      $exist = false;
    }
    $this->db->liberar($sql);
    $this->db->close();

    return $exist;
  }

  public function __destruct() {
    parent::__destruct();
  }

}

?>
