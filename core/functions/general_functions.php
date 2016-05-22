<?php

function redir(string $url = 'index.php') {
  header('location: ' . $url);
}

function send_mail(string $email, string $name, string $HTML, string $titulo) {
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
  $mail->setFrom(PHPMAILER_USER, APP_TITLE);
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

function IsImage(string $file_name) : bool {
  $formats = ['jpg','png','jpeg','gif','JPG','PNG','JPEG','GIF'];
  $file_name = explode('.',$file_name);
  $ext = end($file_name);
  if(in_array($ext,$formats)){
    return true;
  }
  return false;
}

function CheckExists(int $id, string $table, string $DATABASE = DB_NAME) {
  $db = new Conexion($DATABASE);
  $sql = $db->query("SELECT * FROM $table WHERE id='$id' LIMIT 1;");
  if($db->rows($sql) > 0) {
    $exist = $db->recorrer($sql);
  } else {
    $exist = false;
  }
  $db->liberar($sql);
  $db->close();

  return $exist;
}


?>
