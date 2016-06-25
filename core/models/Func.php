<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

final class Func {

  /**
    * Calcula el porcentaje de una cantidad
    *
    * @param int $por: El porcentaje a evaluar, por ejemplo 1, 20, 30 % sin el "%", sólamente el número
    * @param int $n: El número al cual se le quiere sacar el porcentaje
    *
    * @return int con el porcentaje correspondiente
  */
  final public static function percent(int $por, int $n) : int {
    return $n * ($por / 100);
  }

  //------------------------------------------------

  /**
    * Da unidades de peso a un integer según sea su tamaño asumida en bytes
    *
    * @param int $size: Un entero que representa el tamaño a convertir
    *
    * @return string del tamaño $size convertido a la unidad más adecuada
  */
  final public static function convert(int $size) : string {
      $unit = array('bytes','kb','mb','gb','tb','pb');
      return round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
  }

  //------------------------------------------------

  /**
    * Redirecciona a una URL
    *
    * @param string $url: Sitio a donde redireccionará
    *
    * @return void
  */
  final public static function redir(string $url = URL) {
    header('location: ' . $url);
  }

  //------------------------------------------------

  /**
    * Envía un correo electrónico utilizando PHPMailer
    *
    * @param string $email: Destinatario
    * @param string $name: Nombre del destinatario
    * @param string $HTML: Contenido en HTML del email
    * @param string $titulo: Asunto del email
    *
    * @return true si fue enviado correctamente, string con el Error descrito por PHPMailer
  */
  final public static function send_mail(string $email, string $name, string $HTML, string $titulo) {
    $mail = new PHPMailer;
    $mail->CharSet = "UTF-8";
    $mail->Encoding = "quoted-printable";
    //$mail->isSendMail();
    $mail->isSMTP(); # Comentar y descomentar la línea de arriba si da problemas #
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

  //------------------------------------------------

  /**
    * Método privado, su uso está reservado para Func::Paginador(...), devuelve un listado de números por izquierda y derecha según
    * la posición de la página actual en el <ul> del paginador
    *
    * @param string $link: Formato de URL de continuidad para el paginador, ejemplo ?view=buscar&categoria=1
    * @param int $total_pags: Número de páginas totales
    *
    * @return string con los números de las páginas para el paginador
  */
  final private static function get_number_pags(string $link, int $total_pags) : string {
    $paginador = '';
    $max_show = 9; #max_show SIEMPRE debe ser izquierda + derecha + 1
    $izquierda = 4; #numeros máximos a mostrar por la izquierda
    $derecha = 4; #numeros máximos a mostrar por la derecha

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

  //------------------------------------------------

  /**
    * Devuelve un paginador numérico de la forma [<< anterior - 1 - 2 - 3 - *4* - 5 - 6 - 7 - siguiente >>]
    * Para que este funcione, el identificador GET para pasar entre páginas debe ser $_GET['pag'], es decir &pag=numero
    *
    * @param string $link: Formato de URL de continuidad para el paginador, ejemplo ?view=buscar&categoria=1
    * @param int $total_pags: Número de páginas totales
    *
    * @return string con el paginador compatible con bootstrap
  */
  final public static function pager(string $link, int $total_pags) {

    $lng_prev = 'Anterior';
    $lng_next = 'Siguiente';

    $paginador = '<div class="pagination pagination-sm"><ul>';
        if(!isset($_GET['pag']) or !is_numeric($_GET['pag'])) {
          $paginador .= '<li class="disabled"> <a>'.$lng_prev.'</a> </li>';
          $paginador .= self::get_number_pags($link,$total_pags);
          if($total_pags > 1) {
            $paginador .= '<li> <a href="'. $link .'&pag=2"> '.$lng_next.'</a> </li>';
          } else {
            $paginador .= '<li class="disabled"> <a> '.$lng_next.' </a> </li>';
          }
        } else {
          if($total_pags > 1) {
            if($_GET['pag'] > 1 and $_GET['pag'] < $total_pags) {
              $paginador .= '<li> <a href="'. $link .'&pag='. ($_GET['pag'] - 1) .'">'.$lng_prev.'</a> </li>'; //Atrás
              $paginador .= self::get_number_pags($link,$total_pags);
              $paginador .= '<li> <a href="'. $link .'&pag='. ($_GET['pag'] + 1) .'"> '.$lng_next.' </a> </li>'; //Siguiente
            } else if($_GET['pag'] == 1) {
              $paginador .= '<li class="disabled"> <a>'.$lng_prev.'</a> </li>'; //Atrás
              $paginador .= self::get_number_pags($link,$total_pags);
              $paginador .= '<li> <a href="'. $link .'&pag='. ($_GET['pag'] + 1) .'"> '.$lng_next.' </a> </li>'; //Siguiente
            } else {
              $paginador .= '<li> <a href="'. $link .'&pag='. ($_GET['pag'] - 1) .'">'.$lng_prev.'</a> </li>'; //Atrás
              $paginador .= self::get_number_pags($link,$total_pags);
              $paginador .= '<li class="disabled"> <a> '.$lng_next.' </a> </li>'; //Siguiente
            }
          } else {
            $paginador .= '<li class="disabled"> <a> '.$lng_prev.'</a> </li>'; //Atrás
            $paginador .= '<li class="disabled"><a>1</a></li>';
            $paginador .= '<li class="disabled"> <a> '.$lng_next.'  </a> </li>'; //Siguiente
          }
        }
    $paginador .= '</ul></div>';

    return $paginador;
  }

  //------------------------------------------------

  /**
    * Retorna la URL de un gravatar, según el email
    *
    * @param string  $email: El email del usuario a extraer el gravatar
    * @param int $size: El tamaño del gravatar
    * @return string con la URl
  */
   final public static function get_gravatar(string $email, int $size = 32) : string  {
       return 'http://www.gravatar.com/avatar/' . md5($email) . '?s=' . (int) abs($size);
   }


}

?>
