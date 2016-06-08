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

  #devuelve un hash muy seguro
  final public static function hash(string $passw) : string {
    return crypt($password, '$2a$10$' . substr(sha1(mt_rand()),0,22));
  }

  #redirecciona
  final public static function redir(string $url = 'index.php') {
    header('location: ' . $url);
  }

  #envia un correo electrónico a $email, con el nombre $name, con el contenido $HTML y el asunto $titulo
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

  #comprueba si un elemento es una imagen o no
  final public static function IsImage(string $file_name) : bool {
    $formats = ['jpg','png','jpeg','gif','JPG','PNG','JPEG','GIF'];
    $file_name = explode('.',$file_name);
    $ext = end($file_name);
    if(in_array($ext,$formats)){
      return true;
    }
    return false;
  }

  #convierte codigo BBcode en HTML
  final public static function BBCode(string $string) : string {
    $BBcode = array(
        '/\[i\](.*?)\[\/i\]/is',
        '/\[b\](.*?)\[\/b\]/is',
        '/\[u\](.*?)\[\/u\]/is',
        '/\[s\](.*?)\[\/s\]/is',
        '/\[img\](.*?)\[\/img\]/is',
        '/\[center\](.*?)\[\/center\]/is',
        '/\[h1\](.*?)\[\/h1\]/is',
        '/\[h2\](.*?)\[\/h2\]/is',
        '/\[h3\](.*?)\[\/h3\]/is',
        '/\[h4\](.*?)\[\/h4\]/is',
        '/\[h5\](.*?)\[\/h5\]/is',
        '/\[h6\](.*?)\[\/h6\]/is',
        '/\[quote\](.*?)\[\/quote\]/is',
        '/\[url=(.*?)\](.*?)\[\/url\]/is',
        '/\[bgcolor=(.*?)\](.*?)\[\/bgcolor\]/is',
        '/\[color=(.*?)\](.*?)\[\/color\]/is',
        '/\[bgimage=(.*?)\](.*?)\[\/bgimage\]/is',
        '/\[size=(.*?)\](.*?)\[\/size\]/is',
        '/\[font=(.*?)\](.*?)\[\/font\]/is'
    );

    $HTML = array(
        '<i>$1</i>',
        '<b>$1</b>',
        '<u>$1</u>',
        '<s>$1</s>',
        '<img src="$1" />',
        '<center>$1</center>',
        '<h1>$1</h1>',
        '<h2>$1</h2>',
        '<h3>$1</h3>',
        '<h4>$1</h4>',
        '<h5>$1</h5>',
        '<h6>$1</h6>',
        '<blockquote style="background:#f1f5f7;color:#404040;padding:4px;border-radius:4px;">$1</blockquote>',
        '<a href="$1" target="_blank">$2</a>',
        '<div style="background: $1;">$2</div>',
        '<span style="color: $1;">$2</span>',
        '<div style="background: url(\'$1\');">$2</div>',
        '<span style="font-size: $1px">$2</span>',
        '<span style="font-family: $1">$2</span>'
    );

    return nl2br(preg_replace($BBcode,$HTML,$string));
  }

  #uso private para el paginador
  final private static function GetNumberPags(string $link, string $total_pags) : string {
    $paginador = '';
    $max_show = 9;
    $izquierda = 4;
    $derecha = 4;

    if($max_show >= $total_pags) {
      for($x = 1; $x <= $total_pags; $x++) {
        if(isset($_GET['pag']) and $_GET['pag'] == $x) {
          $paginador .= '<li class="active"><a href="'. $link .'&pag='. $x .'">'. $x .'</a></li>'; //Página activa
        } else {
          $paginador .= '<li><a href="'. $link .'&pag='. $x .'">'. $x .'</a></li>';
        }
      }
    } else {
      $actual_pag = isset($_GET['pag']) ? $_GET['pag'] : 1;
      $inicio = $actual_pag - $izquierda;
      if(($actual_pag + $derecha) > $total_pags) {
        $fin = $total_pags;
      } else {
        $fin = $actual_pag + $derecha;
      }

      if($inicio <= 0) {
        for($x = 1; $x <= $max_show; $x++) {
          if(isset($_GET['pag']) and $_GET['pag'] == $x) {
            $paginador .= '<li class="active"><a href="'. $link .'&pag='. $x .'">'. $x .'</a></li>'; //Página activa
          } else {
            $paginador .= '<li><a href="'. $link .'&pag='. $x .'">'. $x .'</a></li>';
          }
        }
      } else {
        for($x = $inicio; $x <= $fin; $x++) {
          if(isset($_GET['pag']) and $_GET['pag'] == $x) {
            $paginador .= '<li class="active"><a href="'. $link .'&pag='. $x .'">'. $x .'</a></li>'; //Página activa
          } else {
            $paginador .= '<li><a href="'. $link .'&pag='. $x .'">'. $x .'</a></li>';
          }
        }
      }
    }

    return $paginador;
  }

  #devuelve un paginador con la lógica implementada directamente para la vista
  final public static function Paginador(string $link, string $total_pags) {

    $paginador = '<div class="pagination pagination-sm"><ul>'; //Varía de acuerdo a si utiliza bootstrap o materialize
        if(!isset($_GET['pag']) or !is_numeric($_GET['pag'])) {
          $paginador .= '<li class="disabled"> <a>'.$_LNG['prev'].'</a> </li>';
          $paginador .= self::GetNumberPags($link,$total_pags);
          if($total_pags > 1) {
            $paginador .= '<li> <a href="'. $link .'&pag=2"> '.$_LNG['next'].'</a> </li>';
          } else {
            $paginador .= '<li class="disabled"> <a> '.$_LNG['next'].' </a> </li>';
          }
        } else {
          if($total_pags > 1) {
            if($_GET['pag'] > 1 and $_GET['pag'] < $total_pags) {
              $paginador .= '<li> <a href="'. $link .'&pag='. ($_GET['pag'] - 1) .'">'.$_LNG['prev'].'</a> </li>'; //Atrás
              $paginador .= self::GetNumberPags($link,$total_pags);
              $paginador .= '<li> <a href="'. $link .'&pag='. ($_GET['pag'] + 1) .'"> '.$_LNG['next'].' </a> </li>'; //Siguiente
            } else if($_GET['pag'] == 1) {
              $paginador .= '<li class="disabled"> <a>'.$_LNG['prev'].'</a> </li>'; //Atrás
              $paginador .= self::GetNumberPags($link,$total_pags);
              $paginador .= '<li> <a href="'. $link .'&pag='. ($_GET['pag'] + 1) .'"> '.$_LNG['next'].' </a> </li>'; //Siguiente
            } else {
              $paginador .= '<li> <a href="'. $link .'&pag='. ($_GET['pag'] - 1) .'">'.$_LNG['prev'].'</a> </li>'; //Atrás
              $paginador .= self::GetNumberPags($link,$total_pags);
              $paginador .= '<li class="disabled"> <a> '.$_LNG['next'].' </a> </li>'; //Siguiente
            }
          } else {
            $paginador .= '<li class="disabled"> <a> '.$_LNG['prev'].'</a> </li>'; //Atrás
            $paginador .= '<li class="disabled"><a>1</a></li>';
            $paginador .= '<li class="disabled"> <a> '.$_LNG['next'].'  </a> </li>'; //Siguiente
          }
        }
    $paginador .= '</ul></div>';

    return $paginador;
  }

  #chequea si un elemento existe en una tabla, si existe devuelve todo el contenido elegido
  final public function CheckExists(int $id, string $table, string $e = '*') {
    $sql = $this->db->query("SELECT $e FROM $table WHERE id='$id' LIMIT 1;");
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
