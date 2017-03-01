<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

final class Func extends Twig_Extension {

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
    * Retorna la URL de un gravatar, según el email
    *
    * @param string  $email: El email del usuario a extraer el gravatar
    * @param int $size: El tamaño del gravatar
    * @return string con la URl
  */
   final public static function get_gravatar(string $email, int $size = 32) : string  {
       return 'http://www.gravatar.com/avatar/' . md5($email) . '?s=' . (int) abs($size);
   }

   //------------------------------------------------

   /**
     * Alias de Empty, más completo
     *
     * @param midex $var: Variable a analizar
     *
     * @return true si está vacío, false si no, un espacio en blanco cuenta como vacío
   */
   final static function emp($var) : bool {
     return (isset($var) && empty(trim(str_replace(' ','',$var))));
   }

   //------------------------------------------------

   /**
     * Aanaliza que TODOS los elementos de un arreglo estén llenos, útil para analizar por ejemplo que todos los elementos de un formulario esté llenos
     * pasando como parámetro $_POST
     *
     * @param array $array, arreglo a analizar
     *
     * @return true si están todos llenos, false si al menos uno está vacío
   */
   final static function all_full(array $array) : bool {
     foreach($array as $e) {
       if(self::emp($e) and $e != '0') {
         return false;
       }
     }
     return true;
   }

   //------------------------------------------------

   /**
     * Alias de Empty() pero soporta más de un parámetro
     *
     * @param infinitos parámetros
     *
     * @return true si al menos uno está vacío, false si todos están llenos
   */
    final public static function e() : bool  {
      for ($i = 0, $nargs = func_num_args(); $i < $nargs; $i++) {
        if(self::emp(func_get_arg($i)) and func_get_arg($i) != '0') {
          return true;
        }
      }
      return false;
    }

    //------------------------------------------------

    /**
      * Alias de date() pero devuele días y meses en español
      *
      * @param string $format: Formato de salida (igual que en date())
      * @param int $time: Tiempo, por defecto es time() (igual que en date())
      *
      * @return string con la fecha en formato humano (y en español)
    */
     final public static function fecha(string $format, int $time = 0) : string  {
       $date = date($format,$time == 0 ? time() : $time);
       $cambios = array(
         'Monday'=> 'Lunes',
         'Tuesday'=> 'Martes',
         'Wednesday'=> 'Miércoles',
         'Thursday'=> 'Jueves',
         'Friday'=> 'Viernes',
         'Saturday'=> 'Sábado',
         'Sunday'=> 'Domingo',
         'January'=> 'Enero',
         'February'=> 'Febrero',
         'March'=> 'Marzo',
         'April'=> 'Abril',
         'May'=> 'Mayo',
         'June'=> 'Junio',
         'July'=> 'Julio',
         'August'=> 'Agosto',
         'September'=> 'Septiembre',
         'October'=> 'Octubre',
         'November'=> 'Noviembre',
         'December'=> 'Diciembre',
         'Mon'=> 'Lun',
         'Tue'=> 'Mar',
         'Wed'=> 'Mie',
         'Thu'=> 'Jue',
         'Fri'=> 'Vie',
         'Sat'=> 'Sab',
         'Sun'=> 'Dom',
         'Jan'=> 'Ene',
         'Aug'=> 'Ago',
         'Apr'=> 'Abr',
         'Dec'=> 'Dic'
       );
       return str_replace(array_keys($cambios),array_values($cambios),$date);
     }

    //------------------------------------------------

  /**
   * Se obtiene de Twig_Extension y sirve para que cada función esté disponible como etiqueta en twig
    *
   * @return array: Todas las funciones con sus respectivos nombres de acceso en plantillas twig
  */
   public function getFunctions() : array {
      return array(
       new Twig_Function('percent', array($this, 'percent')),
       new Twig_Function('convert', array($this, 'convert')),
       new Twig_Function('get_gravatar', array($this, 'get_gravatar')),
       new Twig_Function('emp', array($this, 'emp')),
       new Twig_Function('e_dynamic', array($this, 'e')),
       new Twig_Function('all_full', array($this, 'all_full')),
       new Twig_Function('fecha', array($this, 'fecha'))
     );
   }

   //------------------------------------------------

  /**
    * Identificador único para la extensión de twig
    *
    * @return string: Nombre de la extensión
  */
  public function getName() : string {
     return 'ocrend_framework_func_class';
  }

}

?>
