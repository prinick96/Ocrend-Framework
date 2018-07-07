<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ocrend\Kernel\Helpers;

/**
 * Helper con funciones útiles para tratar strings.
 *
 * @author Brayan Narváez <prinick@ocrend.com>
 */

final class Strings extends \Twig_Extension {

  /**
   * Convierte un tiempo dado al formato hace 1 minuto, hace 2 horas, hace 1 año ...
   *
   * @param int $from: Tiempo en segundo desde donde se desea contar
   * @param int|null $to: Tiempo en segundo hasta donde se desea contar, si no se pasa por defecto es el tiempo actual
   *
   * @return string con la forma: hace 20 segundos, hace 1 minuto, hace 2 horas, hace 4 días, hace 1 semana, hace 3 meses, hace 1 año ...
  */
  public static function amigable_time(int $from, $to = null) : string {   
    $intervalos = ['segundo', 'minuto', 'hora', 'día', 'semana', 'mes', 'año'];
    $duraciones = ['60','60','24','7','4.35','12'];
    $to = $to ?? time();

    $diferencia = $from - $to;
    $tiempo = 'Dentro de';
    if($to > $from) {   
        $diferencia = $to - $from;
        $tiempo = 'Hace';
    }
    
    for($j = 0; $diferencia >= $duraciones[$j] && $j < sizeof($duraciones) - 1 ; $j++) {
      $diferencia /= $duraciones[$j];
    }
    
    $diferencia = round($diferencia);
    
    if($diferencia != 1) {
      $intervalos[5].= 'e'; //MESES
      $intervalos[$j].= 's';
    }
   
    return $tiempo . ' ' . $diferencia . ' ' . $intervalos[$j];
  }
  
  /**
   * Compara un string hash con un string sin hash, si el string sin hash al encriptar posee la misma llave que hash, son iguales
   *
   * @param string $hash: Hash con la forma $2a$10$87b2b603324793cc37f8dOPFTnHRY0lviq5filK5cN4aMCQDJcC9G
   * @param string $s2: Cadena de texto a comparar
   *
   * @example Strings::chash('$2a$10$87b2b603324793cc37f8dOPFTnHRY0lviq5filK5cN4aMCQDJcC9G','123456'); //return true
   *
   * @return bool true si $s2 contiene la misma llave que $hash, por tanto el contenido de $hash es $s2, de lo contrario false
  */
  public static function chash(string $hash, string $s2) : bool  {
    return $hash == crypt($s2, substr($hash, 0, 29));
  }

  /**
   * Encripta un string, utilizando una llave para posteriormente poder desencriptar
   *
   * @param string $str: Cadena a encriptar
   * @param string $key: Llave única para poder encriptar
   * 
   * @return string : Texto encriptado
   */
  public static function ocrend_encode(string $str, string $key) : string {
    $___s___ = openssl_encrypt($str,
    "AES-128-ECB",
    $key);

    return base64_encode($___s___);
  }

  /**
   * Desencripta un string, utilizando una llave que se ocupó al encriptar
   *
   * @param string $str: Cadena a desencriptar
   * @param string $key: Llave única para poder desencriptar
   * 
   * @return string : Texto desencriptado
   */
  public static function ocrend_decode(string $str, string $key) : string {
    $data = base64_decode($str);
    
    return openssl_decrypt($data,
    "AES-128-ECB",
    $key);
  }

  /**
   * Devuelve un hash DINÁMICO, para comparar un hash con un elemento se utiliza chash
   *
   * @param string $p: Cadena de texto a encriptar
   *
   * @return string Hash, con la forma $2a$10$87b2b603324793cc37f8dOPFTnHRY0lviq5filK5cN4aMCQDJcC9G
   */
  public static function hash(string $p) : string {
    return crypt($p, '$2a$10$' . substr(sha1(mt_rand()), 0, 22));
  }
  
  /**
   * Calcula el tiempo de diferencia entre dos fechas
   *
   * @param string $ini: Fecha menor con el formato d-m-Y ó d/m/Y
   * @param string $fin: Fecha mayor con el formato d-m-Y ó d/m/Y
   *
   * @return int con la diferencia de tiempo en días
   *
   */
  public static function date_difference(string $ini, string $fin) : int {
    $ini_i = explode('-',str_replace('/','-',$ini));
    $fin_i = explode('-',str_replace('/','-',$fin));
    return (int) floor((mktime(0, 0, 0, $fin_i[1], $fin_i[0], $fin_i[2]) - mktime(0, 0, 0, $ini_i[1], $ini_i[0], $ini_i[2])) / 86400);
  }

  /**
   * Calcula la edad de una persona segun la fecha de nacimiento
   *
   * @param string $cumple: Fecha de nacimiento con el formato d-m-Y ó d/m/Y
   *
   * @return int con la edad
   *
   */
  public static function calculate_age(string $cumple) : int {
    $age = explode('.', (string) (self::date_difference($cumple, date('d-m-Y', time()))/365));
    return (int) $age[0];
  }

  /**
   * Calcula cuántos días tiene el mes actual
   *
   * @return integer con la cantidad de días del mes
   *
   */
  public static function days_of_month() : int {
    return cal_days_in_month(CAL_GREGORIAN, (int) date('m',time()), (int) date('Y',time()));
  }

  /**
   * Verifica si una cadena de texto tiene forma de email
   *
   * @param string $address: Cadena de texto con el email
   *
   * @return mixed devuelve TRUE si es un email y FALSE si no lo es
   */
  public static function is_email(string $address) {
    return filter_var($address, FILTER_VALIDATE_EMAIL);
  }

  /**
    * Remueve todos los espacios en blanco de un string
    *
    * @param string $s: Cadena de texto a convertir
    *
    * @return string del texto sin espacios
  */
  public static function remove_spaces(string $s) : string {
    return trim(str_replace(' ', '', $s));
  }

  /**
   * Analiza si una cadena de texto es alfanumérica
   *
   * @param string $s: Cadena de texto a verificar
   *
   * @return bool, verdadero si es alfanumerica, falso si no
  */
  public static function alphanumeric(string $s) : bool {
    return ctype_alnum(self::remove_spaces($s));
  }


  /**
   * Analiza si una cadena de texto verificando si sólamente tiene letras
   *
   * @param string $s: Cadena de texto a verificar
   *
   * @return bool, verdadero si sólamente tiene letras, falso si no
  */
  public static function only_letters(string $s) : bool {
    return ctype_alpha(self::remove_spaces($s));
  }


  /**
   * Analiza si una cadena de texto contiene sólamente letras y números
   *
   * @param string $s: Cadena de texto a verificar
   *
   * @return bool, verdadero si sólamente contiene letras y números, falso si no
   */
  public static function letters_and_numbers(string $s) : bool {
    return (boolean) preg_match('/^[\w.]*$/', self::remove_spaces($s));
  }


  /**
   * Convierte una expresión de texto, a una compatible con url amigables
   *
   * @param string $url: Cadena de texto a convertir
   *
   * @return string Cadena de texto con formato de url amigable
  */
  public static function url_amigable(string $url) : string {
    $url = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], $url);
    $url = str_replace([' ', '&', '\r\n', '\n', '+', '%'], '-', $url);
    return strtolower(preg_replace(['/[^a-zA-Z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/'], ['', '-', ''], $url));
  }
 

  /**
   * Convierte código BBCode en su equivalente HTML
   *
   * @param string $string: Código con formato BBCode dentro
   *
   * @return string del código BBCode transformado en HTML
   */
  public static function bbcode(string $string) : string {
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
    return nl2br(preg_replace($BBcode, $HTML, $string));
  }

  /**
   * Dice si un string comienza con un caracter especificado
   *
   * @param string $sx: Caracter de inicio
   * @param string $str: String a evaluar
   * @param bool $case_sensitive: Boolean para definir si será seible a mayúsculas o no
   *
   * @return bool True si comienza con el caracter especificado, False si no
   */
  public static function begin_with(string $sx, string $str) : bool {
    return (bool) (strlen($str) > 0 and $str[0] == $sx);
  }
 
  /**
   * Dice si un string termina con una caracter especificado
   *
   * @param string $sx: Caracter del final
   * @param string $str: String a evaluar
   *
   * @return bool True si termina con el caracter especificado, False si no
   */
  public static function end_with(string $sx, string $str) : bool {
    return (bool) (strlen($str) > 0 and $str[strlen($str) - 1] == $sx);
  }

  /**
   * Ver si un string está contenido en otro
   *
   * @param $s: String contenido en $str
   * @param $str: String a evaluar
   *
   * @return bool True si $s está dentro de $str, False si no
   */
  public static function contain(string $s, string $str) : bool {
    return (bool) (strpos($str, $s) !== false);
  }

  /**
   * Devuelve la cantidad de palabras en un string
   *
   * @param $str: String a evaluar
   *
   * @return int Cantidad de palabras
   */
  public static function count_words(string $s) : int {
    return (int) str_word_count($s,0,'0..9_');
  }

  /**
   * Se obtiene de Twig_Extension y sirve para que cada función esté disponible como etiqueta en twig
   *
   * @return array Todas las funciones con sus respectivos nombres de acceso en plantillas twig
   */
  public function getFunctions() : array {
    return array(
      new \Twig_Function('amigable_time', array($this, 'amigable_time')),
      new \Twig_Function('chash', array($this, 'chash')),
      new \Twig_Function('hash', array($this, 'hash')),
      new \Twig_Function('date_difference', array($this, 'date_difference')),
      new \Twig_Function('calculate_age', array($this, 'calculate_age')),
      new \Twig_Function('days_of_month', array($this, 'days_of_month')),
      new \Twig_Function('is_email', array($this, 'is_email')),
      new \Twig_Function('remove_spaces', array($this, 'remove_spaces')),
      new \Twig_Function('alphanumeric', array($this, 'alphanumeric')),
      new \Twig_Function('only_letters', array($this, 'only_letters')),
      new \Twig_Function('letters_and_numbers', array($this, 'letters_and_numbers')),
      new \Twig_Function('url_amigable', array($this, 'url_amigable')),
      new \Twig_Function('bbcode', array($this, 'bbcode')),
      new \Twig_Function('begin_with', array($this, 'begin_with')),
      new \Twig_Function('end_with', array($this, 'end_with')),
      new \Twig_Function('contain', array($this, 'contain')),
      new \Twig_Function('count_words', array($this, 'count_words'))
    );
  }

  /**
   * Identificador único para la extensión de twig
   *
   * @return string Nombre de la extensión
  */
  public function getName() : string {
    return 'ocrend_framework_helper_strings';
  }
}