<?php

/**
  * Las funciones que requiren conexión con la base de datos o heredar algo del modelo, su previa llamda debe realizarse
  * instanciando primero la clase Func, y llamarse a través de un objeto.
  *
  * Las que no, son static y utilizan Func::nombrefuncion()
*/

final class Func extends Models implements OCREND {

  public function __construct() {
    parent::__construct();
  }

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

  /**
    * Compara un string hash con un string sin hash, si el string sin hash al encriptar posee la misma llave que hash, son iguales
    *
    * @param string $hash: Hash con la forma $2a$10$87b2b603324793cc37f8dOPFTnHRY0lviq5filK5cN4aMCQDJcC9G
    * @param string $s2: Cadena de texto a comparar
    *
    * @example Func::chash('$2a$10$87b2b603324793cc37f8dOPFTnHRY0lviq5filK5cN4aMCQDJcC9G','123456'); //return true
    *
    * @return true si $s2 contiene la misma llave que $hash, por tanto el contenido de $hash es $s2, de lo contrario false
  */
  final public static function chash(string $hash, string $s2) : bool {
    $full_salt = substr($hash, 0, 29);
    $new_hash = crypt($s2, $full_salt);
    return ($hash == $new_hash);
   }

  /**
    * Devuelve un hash DINÁMICO, para comparar un hash con un elemento se utiliza chash
    *
    * @param string $p: Cadena de texto a encriptar
    *
    * @return string Hash, con la forma $2a$10$87b2b603324793cc37f8dOPFTnHRY0lviq5filK5cN4aMCQDJcC9G
  */
  final public static function hash(string $p) : string {
    return crypt($p, '$2a$10$' . substr(sha1(mt_rand()),0,22));
  }

  /**
    * Redirecciona a una URL
    *
    * @param string $url: Sitio a donde redireccionará
    *
    * @return void
  */
  final public static function redir(string $url = 'index.php') {
    header('location: ' . $url);
  }

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
    $mail->isSendMail(); # Comentar y descomentar la línea de abajo si da problemas #
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

  /**
    * Dice si un elemento es una imagen o no según su extensión
    *
    * @param string $file_name: Nombre del archivo, da igual si es solo el nombre o la ruta con el nombre
    *
    * @return true si es una imagen, false si no lo es
  */
  final public static function is_image(string $file_name) : bool {
    $formats = ['jpg','png','jpeg','gif','JPG','PNG','JPEG','GIF'];
    $file_name = explode('.',$file_name);
    $ext = end($file_name);

    return in_array($ext,$formats);
  }

  /**
    * Remueve todos los espacios en blanco de un string
    *
    * @param string $s: Cadena de texto a convertir
    *
    * @return string del texto sin espacios
  */
  final public static function remove_spaces(string $s) : string {
    return str_replace(' ','',$s);
  }

  /**
    * Analiza si una cadena de texto es alfanumérica
    *
    * @param string $s: Cadena de texto a verificar
    *
    * @return bool, verdadero si es alfanumerica, falso si no
  */
  final public static function alphanumeric(string $s) : bool {
    $s = self::remove_spaces($s);
    return ctype_alnum($s);
  }

  /**
    * Analiza si una cadena de texto verificando si sólamente tiene letras
    *
    * @param string $s: Cadena de texto a verificar
    *
    * @return bool, verdadero si sólamente tiene letras, falso si no
  */
  final public static function only_letters(string $s) : bool {
    $s = self::remove_spaces($s);
    return ctype_alpha($s);
  }

  /**
    * Analiza si una cadena de texto contiene sólamente letras y números
    *
    * @param string $s: Cadena de texto a verificar
    *
    * @return bool, verdadero si sólamente contiene letras y números, falso si no
  */
  final public static function letters_and_numbers(string $s) : string {
    $s = self::remove_spaces($s);
    return preg_match('/^[\w.]*$/', $s);
  }

  /**
    * Convierte una expresión de texto, a una compatible con url amigables
    *
    * @param string $url: Cadena de texto a convertir
    *
    * @return string Cadena de texto con formato de url amigable
  */
  final public function url_amigable(string $url) : string {
    $url = strtolower($url);
    $url = str_replace (['á', 'é', 'í', 'ó', 'ú', 'ñ'],['a', 'e', 'i', 'o', 'u', 'n'], $url);
    $url = str_replace([' ', '&', '\r\n', '\n', '+', '%'],'-',$url);

    return preg_replace (['/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/'],['', '-', ''], $url);
  }

  /**
    * Convierte código BBCode en su equivalente HTML
    *
    * @param string $string: Código con formato BBCode dentro
    *
    * @return string del código BBCode transformado en HTML
  */
  final public static function bbcode(string $string) : string {
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

  /**
    * Prueba la existencia de un elemento en la base de datos
    * Para que este funcione, el identificador GET para pasar entre páginas debe ser $_GET['pag'], es decir &pag=numero
    * IMPORTANTE: Debe estar INSTANCIADA, la clase Func y a través de un objeto se invoca esta función
    *
    * @param int $id: Id del elemento en la base de datos
    * @param string $table: Tabla en donde se quiere verificar
    * @param string $e: Elementos a seleccionar
    *
    * @return si existe devuelve el contenido en un arreglo asociativo/numérico, si no deveuvel false
  */
  final public function check_exists(int $id, string $table, string $e = '*') {
    return $this->db->select($e,$table,"id='$id','LIMIT 1'");
  }

  public function __destruct() {
    parent::__destruct();
  }

}

?>
