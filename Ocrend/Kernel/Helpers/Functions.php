<?php

/*
 * This file is part of the Ocrend Framewok 2 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ocrend\Kernel\Helpers;

/**
 * Funciones reutilizables dentro del sistema.
 *
 * @author Brayan Narváez <prinick@ocrend.com>
 */

final class Functions extends \Twig_Extension {

   /**
      * Verifica parte de una fecha, método privado usado en str_to_time
      * 
      * @param int $index: Índice del arreglo
      * @param array $detail: Arreglo
      * @param int $max: Valor a comparar
      *
      * @return bool con el resultado de la comparación
   */
  final private function check_str_to_time(int $index, array $detail, int $max) : bool {
    return !array_key_exists($index,$detail) || !is_numeric($detail[$index]) || intval($detail[$index]) < $max;
  }

   //------------------------------------------------

    /**
     * Redirecciona a una URL
     *
     * @param string $url: Sitio a donde redireccionará, si no se pasa, por defecto
     * se redirecciona a la URL principal del sitio
     *
     * @return void
     */
  final public function redir($url = null) {
    global $config;
    
    if (null == $url) {
      $url = $config['site']['url'];
    }
    
    \Symfony\Component\HttpFoundation\RedirectResponse::create($url)->send();
    exit(1);
  }

  //------------------------------------------------

  /**
   * Calcula el porcentaje de una cantidad
   *
   * @param float $por: El porcentaje a evaluar, por ejemplo 1, 20, 30 % sin el "%", sólamente el número
   * @param float $n: El número al cual se le quiere sacar el porcentaje
   *
   * @return float con el porcentaje correspondiente
   */
  final public function percent(float $por, float $n) : float {
    return $n*($por/100);
  }

  //------------------------------------------------

  /**
   * Da unidades de peso a un integer según sea su tamaño asumida en bytes
   *
   * @param int $size: Un entero que representa el tamaño a convertir
   *
   * @return string del tamaño $size convertido a la unidad más adecuada
   */
  final public function convert(int $size) : string {
      $unit = array('bytes', 'kb', 'mb', 'gb', 'tb', 'pb');
      return round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
  }

  //------------------------------------------------

  /**
    * Retorna la URL de un gravatar, según el email
    *
    * @param string  $email: El email del usuario a extraer el gravatar
    * @param int $size: El tamaño del gravatar
    * @return string con la URl
  */
   final public function get_gravatar(string $email, int $size = 32) : string  {
       return 'http://www.gravatar.com/avatar/' . md5($email) . '?s=' . (int) abs($size);
   }

   //------------------------------------------------

   /**
     * Alias de Empty, más completo
     *
     * @param mixed $var: Variable a analizar
     *
     * @return bool con true si está vacío, false si no, un espacio en blanco cuenta como vacío
   */
   final public function emp($var) : bool {
     return (isset($var) && empty(trim(str_replace(' ', '', $var))));
   }

   //------------------------------------------------

   /**
     * Aanaliza que TODOS los elementos de un arreglo estén llenos, útil para analizar por ejemplo que todos los elementos de un formulario esté llenos
     * pasando como parámetro $_POST
     *
     * @param array $array, arreglo a analizar
     *
     * @return bool con true si están todos llenos, false si al menos uno está vacío
   */
   final public function all_full(array $array) : bool {
     foreach($array as $e) {
       if($this->emp($e) and $e != '0') {
         return false;
       }
     }
     return true;
   }

   //------------------------------------------------

   /**
     * Alias de Empty() pero soporta más de un parámetro (infinitos)
     *
     * @return bool con true si al menos uno está vacío, false si todos están llenos
   */
    final public function e() : bool  {
      for ($i = 0, $nargs = func_num_args(); $i < $nargs; $i++) {
        if(null === func_get_arg($i) || ($this->emp(func_get_arg($i)) && func_get_arg($i) != '0')) {
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
    final public function fecha(string $format, int $time = 0) : string  {
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
       return str_replace(array_keys($cambios), array_values($cambios), $date);
     }

   //------------------------------------------------

  /**
    *  Devuelve la etiqueta <base> html adecuada para que los assets carguen desde allí.
    *  Se adapta a la configuración del dominio en general.
    *
    * @return string <base href="ruta" />
  */
  final public function base_assets() : string {
    global $config, $http;

    # Revisar subdominio
    $server = $http->server->get('SERVER_NAME');
    $www = $server[0] . $server[1] . $server[2];
    # Revisar protocolo
    $base = $config['site']['router']['protocol'] . '://';

    if (strtolower($www) == 'www') {
      $base .= 'www.' . $config['site']['router']['path'];
    } else {
      $base .= $config['site']['router']['path'];
    }
  
    return '<base href="' . $base . '" />';
  }
  
  //------------------------------------------------

  /**
   * Obtiene el último día de un mes específico
   *
   * @param int $mes: Mes (1 a 12)
   * @param int $anio: Año (1975 a 2xxx)
   *
   * @return string con el número del día
  */
  final public function last_day_month(int $mes, int $anio) : string {
    return date('d', (mktime(0,0,0,$mes + 1, 1, $anio) - 1));
  }

  //------------------------------------------------
  
  /**
   * Pone un cero a la izquierda si la cifra es menor a diez
   *
   * @param int $num: cifra
   *
   * @return string cifra con cero a la izquirda
  */
  final public function cero_izq(int $num) : string {
    if($num < 10) {
      return '0' . $num;
    }

    return $num;
  }

  //------------------------------------------------

   /**
    * Devuelve el timestamp de una fecha, y null si su formato es incorrecto.
    * 
    * @param string|null $fecha: Fecha con formato dd/mm/yy
    * @param string $hora: Hora de inicio de la $fecha
    *
    * @return int|null con el timestamp
  */
  final public function str_to_time($fecha, string $hora = '00:00:00') {
    if(null == $fecha) {
      return null;
    }
    
    $detail = explode('/',$fecha);

    // Formato de día incorrecto
    if($this->check_str_to_time(0,$detail,1)) {
      return null;
    }

    // Formato de mes incorrecto
    if($this->check_str_to_time(1,$detail,1) || intval($detail[1]) > 12) {
      return null;
    }

    // Formato del año
    if($this->check_str_to_time(2,$detail,1970)) {
      return null;
    }

    // Verificar días según año y mes
    $day = intval($detail[0]);
    $month = intval($detail[1]);
    $year = intval($detail[2]);

    // Veriricar dia según mes
    if ($day > $this->last_day_month($month, $year)) {
      return null;
    }

    return strtotime($detail[0] . '-' . $detail[1] . '-' . $detail[2] . ' ' . $hora);
  }

  //------------------------------------------------

  /**
   * Devuelve la fecha en format dd/mm/yyy desde el principio de la semana, mes o año actual.
   *
   * @param int $desde: Desde donde
   *
   * @return mixed
  */
  final public function desde_date(int $desde) {
     # Obtener esta fecha
     $hoy = date('d/m/Y/D',time());
     $hoy = explode('/',$hoy);


    switch($desde) {
      # Hoy
      case 1:
        return date('d/m/Y', time());

      # Ayer
      case 2:
        return date('d/m/Y', time() - (60*60*24));
        
      # Semana
      case 3:
        # Día de la semana actual
        switch ($hoy[3]) {
          case 'Mon':
            $dia = $hoy[0];
          break;
          case 'Tue':
            $dia = intval($hoy[0]) - 1;
          break;
          case 'Wed':
            $dia = intval($hoy[0]) - 2;
          break;
          case 'Thu':
            $dia = intval($hoy[0]) - 3;
          break;
          case 'Fri':
            $dia = intval($hoy[0]) - 4;
          break;
          case 'Sat':
            $dia = intval($hoy[0]) - 5;
          break;
          default: # 'Sun'
            $dia = intval($hoy[0]) - 6;
          break;
        }

        # Mes anterior y posiblemente, año también.
        if($dia == 0) {
          # Verificamos si estamos en enero
          if($hoy[1] == 1) {
            return $this->last_day_month($hoy[1],$hoy[2]) .'/'. $this->cero_izq($hoy[1] - 1) .'/' . ($hoy[2] - 1);
          }
          
          # Si no es enero, es el año actual
          return $this->last_day_month($hoy[1],$hoy[2]) .'/'. $this->cero_izq($hoy[1] - 1) .'/' . $hoy[2];
        }
        
        return $this->cero_izq($dia) .'/'. $this->cero_izq($hoy[1]) .'/' . $hoy[2];

      # Mes
      case 4:
        return '01/'. $this->cero_izq($hoy[1]) .'/' . $hoy[2];
       
      # Año
      case 5:
        return '01/01/' . $hoy[2];

      default:
        throw new \RuntimeException('Problema con el valor $desde en desde_date()');
       break;
    }
  }

  //------------------------------------------------

  /**
   * Obtiene el tiempo actual
   *
   * @return int devuelve time()
  */
  final public function timestamp() : int {
     return time();
  }

  //------------------------------------------------

  /**
   * Se obtiene de Twig_Extension y sirve para que cada función esté disponible como etiqueta en twig
    *
   * @return array con todas las funciones con sus respectivos nombres de acceso en plantillas twig
  */
  public function getFunctions() : array {
      return array(
       new \Twig_Function('percent', array($this, 'percent')),
       new \Twig_Function('convert', array($this, 'convert')),
       new \Twig_Function('get_gravatar', array($this, 'get_gravatar')),
       new \Twig_Function('emp', array($this, 'emp')),
       new \Twig_Function('e_dynamic', array($this, 'e')),
       new \Twig_Function('all_full', array($this, 'all_full')),
       new \Twig_Function('fecha', array($this, 'fecha')),
       new \Twig_Function('base_assets', array($this, 'base_assets')),
       new \Twig_Function('timestamp', array($this, 'timestamp')),
       new \Twig_Function('desde_date', array($this, 'desde_date')),
       new \Twig_Function('cero_izq', array($this, 'cero_izq')),
       new \Twig_Function('last_day_month', array($this, 'last_day_month')),
       new \Twig_Function('str_to_time', array($this, 'str_to_time')),
       new \Twig_Function('desde_date', array($this, 'desde_date'))
     );
   }

   //------------------------------------------------

  /**
      * Identificador único para la extensión de twig
      *
      * @return string con el nombre de la extensión
  */
  public function getName() : string {
        return 'ocrend_framework_func_class';
  }
}
