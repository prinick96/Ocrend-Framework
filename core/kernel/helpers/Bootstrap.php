<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

final class Bootstrap extends Twig_Extension {

  /**
    * Crea un Dropdown de Boostrap
    *
    * @param string $name: Nombre del dropdown
    * @param array $elements: Arreglo con los enlaces del dropdown, de la forma array(
    *                                                                           'nombre' => 'http://url',
    *                                                                           'nombre2' => 'http://url2'
    *                                                                           )
    * @param bool $up: Hace que el dropdown se despliegue hacia arriba
    * @param string $extra_css: Css extra anidado en la etiqueta class
    *
    * @return string con el dropdown realizado
  */
  final public static function dropdown(string $name, array $elements, bool $up = false, string $extra_css = '') : string {

    $links = '';
    foreach ($elements as $e => $url) {
      $links .= '<li><a href="'.$url.'">'.$e.'</a></li>';
    }

    return '<div class="'. ($up ? 'dropup' : 'dropdown') . ' '. $extra_css .'">
      <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        '.$name.'
        <span class="caret"></span>
      </button>
      <ul class="dropdown-menu">
        '.$links.'
      </ul>
    </div>';
  }

  /**
    * Crea un Button Dropdown de Boostrap
    *
    * @param string $name: Nombre del dropdown
    * @param array $elements: Arreglo con los enlaces del dropdown, de la forma array(
    *                                                                           'nombre' => 'http://url',
    *                                                                           'nombre2' => 'http://url2'
    *                                                                           )
    * @param bool $vertical: Hace que el dropdown se despliegue hacia arriba
    * @param string $extra_css: Css extra anidado en la etiqueta class
    *
    * @return string con el dropdown realizado
  */
  final public static function button_dropdown(string $name, array $elements, string $type = 'default', bool $vertical = false, string $extra_css = '') : string {

    $links = '';
    foreach ($elements as $e => $url) {
      $links .= '<li><a href="'.$url.'">'.$e.'</a></li>';
    }

    return '<div class="btn-group '. (!$vertical ?'': 'dropup') .' '. $extra_css .'">
      <button type="button" class="btn btn-'.$type.'">'.$name.'</button>
      <button type="button" class="btn btn-'.$type.' dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only">Desplegar</span>
      </button>
      <ul class="dropdown-menu">
        '.$links.'
      </ul>
    </div>';
  }

  //------------------------------------------------

  /**
    * Botón básico
    *
    * @param string $texto: Texto del botón
    * @param string $type: Tipo de botón, por defecto button
    * @param string $class: Clase de botón, por defecto 'default'
    * @param string $id: Texto a poner en id=""
    * @param string $extra_css: Css extra anidado en la etiqueta class
    *
    * @return string con el botón
  */
  final public static function button(string $texto, string $type = 'button', string $class = 'default', string $id = '', string $extra_css = '') : string {
    return "<button type=\"$type\" class=\"btn btn-$class $extra_css\" id=\"$id\">$texto</button>";
  }

  //------------------------------------------------

  /**
    * Renderiza un input inline básico, sólamente la etiqueta input
    *
    * @param string $type: Tipo de input (tel,email,text,etc.)
    * @param string $name: Nombre para el input, se coloca en name="" e id="id_"
    * @param bool $required: Colocar true si el campo debe tener la etiqueta required=""
    * @param string $value: Valor del input, por defecto vacío
    * @param string $placeholder: Placeholder por defecto vacío
    * @param string $extra_css: Css extra anidado en la etiqueta class
    *
    * @return string input renderizado
  */
  final public static function basic_input(string $type, string $name, bool $required = false, string $value = '',  string $placeholder = '', string $extra_css = '') : string {
    return "<input type=\"$type\" name=\"$name\" id=\"id_$name\" value=\"$value\" placeholder=\"$placeholder\" class=\"form-control $extra_css\" " . (!$required ?'': 'required=""') . "/>";
  }

  //------------------------------------------------

  /**
    * Renderiza un select básico con opciones, sólamente la etiqueta select con sus options
    *
    * @param string $name: Nombre para el select, se coloca en name="" e id="id_"
    * @param array $options: Opciones del select, en forma de array('value' => 'nombre');
    * @param bool $multi: establecer true si va a ser un select múltiple
    * @param mixed $selected: Puede ser un arreglo con todos los valores que están seleccionados con la forma array('opcion1','opcion2')
    * ó un string simple con el value seleccionado, esto hará que se marque la casilla selected en la opción del select
    * @param string $extra_css: Css extra anidado en la etiqueta class
    *
    * @return string input renderizado
  */
  final public static function basic_select(string $name, array $options, bool $multi = false, $selected = '', string $extra_css = '') : string {

    $opt = '';
    if(is_array($selected)) {
      foreach ($options as $value => $n) {
        $opt .= '<option value="'.$value.'"'. (!(in_array($value, $selected)) ?'': 'selected=""') .'>'.$n.'</option>';
      }
    } else {
      foreach ($options as $value => $n) {
        $opt .= '<option value="'.$value.'"'. ($selected != $value ?'': 'selected=""') .'>'.$n.'</option>';
      }
    }

    return '<select name="'.$name.''.(!$multi ?'': '[]').'" id="id_'.$name.'" class="form-control '.$extra_css.'" '.(!$multi ?'': 'multiple').'>
      '.$opt.'
    </select>';
  }

  //------------------------------------------------

  /**
    * Checkbox básico
    *
    * @param string $name: Nombre para el checkbox, se coloca en name="" e id="id_"
    * @param string $value: Valor para el checkbox
    * @param bool $checked: Establecer true para mantener mantener clicado el checkbox
    * @param string $extra_css: Css extra anidado en la etiqueta class
    *
    * @return string con el checkbox
  */
  final public static function checkbox(string $name, string $value, bool $checked = false, string $extra_css = '') : string {
    return "<input type=\"checkbox\" class=\"$extra_css\" name=\"$name\" id=\"id_$name\" value=\"$value\"" . (!$checked ?'': 'checked') . ' />';
  }

  //------------------------------------------------

  /**
    * Radio básico
    *
    * @param string $name: Nombre para el radio, se coloca en name="" e id="id_"
    * @param string $value: Valor para el radio
    * @param bool $checked: Establecer true para mantener mantener clicado el radio
    * @param string $extra_css: Css extra anidado en la etiqueta class
    *
    * @return string con el radio
  */
  final public static function radio(string $name, string $value, bool $checked = false, string $extra_css = '') : string {
    return "<input type=\"radio\" class=\"$extra_css\" name=\"$name\" id=\"id_$name\" value=\"$value\"" . (!$checked ?'': 'checked') . ' />';
  }

  //------------------------------------------------

  /**
    * Renderiza un textarea básico de bootstrap
    *
    * @param string $name: Nombre para el textarea, se coloca en name="" e id="id_"
    * @param string $placeholder: Placeholder por defecto vacío
    * @param string $value: Contenido del textarea por defecto vacío
    * @param bool $required: Colocar true si el campo debe tener la etiqueta required=""
    * @param string $extra_css: Css extra anidado en la etiqueta class
    *
    * @return string
  */
  final public static function textarea(string $name, string $placeholder = '', string $value = '', bool $required = false, string $extra_css = '') : string {
    return "<textarea name=\"$name\" id=\"id_$name\" placeholder=\"$placeholder\" class=\"form-control $extra_css\" " . (!$required ?'': 'required=""') . ">$value</textarea>";
  }

  //------------------------------------------------

  /**
    * Renderiza una alerta
    *
    * @param string $message: Mensaje a mostrar con la alerta
    * @param string $type: danger,success,warning,info por defecto es danger
    * @param bool $boton: true Si se quiere que tenga un botón de cerrar
    * @param string $id: valor que se quiera colocar en el campo id=""
    * @param string $extra_css: Css extra anidado en la etiqueta class
    *
    * @return string alerta renderizada
  */
  final public static function alert(string $message, string $type = 'danger', bool $boton = false, string $id = '', string $extra_css = '') : string {
    return "<div class=\"alert alert-$type $extra_css\" id=\"$id\" role=\"alert\">
    ". (!$boton ? '' : '<button type=\"button\" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>') ."
    $message</div>";
  }

  //------------------------------------------------

  /**
    * FUNCIÓN NO ACCESIBLE, USO ESTRICTO PARA UNA FUNCIÓN INTERNA DEL HELPER
    * Método privado, su uso está reservado para Bootstrap::pager(...), devuelve un listado de números por izquierda y derecha según
    * la posición de la página actual en el <ul> del paginador
    *
    * @param string $link: Formato de URL de continuidad para el paginador, ejemplo /controlador/otras/rutas
    * @param int $total_pags: Número de páginas totales
    * @param int/null $get_pag: Variable que contiene la página actual, tiene que provenir de $this->route probablemente
    * @param array $conf: Arreglo con la forma de la constante PAGER_CONFIG, contiene configuración
    *
    * @return string con los números de las páginas para el paginador
  */
  final private static function get_number_pags(string $link, int $total_pags, $get_pag, array $conf) : string {
    $paginador = '';
    $max_show = $conf['i'] + $conf['d'] + 1;

    if($max_show >= $total_pags) {
      for($x = 1; $x <= $total_pags; $x++) {
        if(null != $get_pag and $get_pag == $x) {
          $paginador .= '<li class="active"><a href="'. $link . $x .'/">'. $x .'</a></li>'; //Página activa
        } else if(null == $get_pag and $x == 1) {
          $paginador .= '<li class="active"><a href="'. $link.'/1/">1</a></li>'; //Página activa
        } else {
          $paginador .= '<li><a href="'. $link . $x .'/">'. $x .'</a></li>';
        }
      }
    } else {
      $actual_pag = $get_pag ? $get_pag : 1;
      $inicio = $actual_pag - $conf['i'];
      if(($actual_pag + $conf['d']) > $total_pags) {
        $fin = $total_pags;
      } else {
        $fin = $actual_pag + $conf['d'];
      }

      if($inicio <= 0) {
        for($x = 1; $x <= $max_show; $x++) {
          if(null != $get_pag and $get_pag == $x) {
            $paginador .= '<li class="active"><a href="'. $link . $x .'/">'. $x .'</a></li>'; //Página activa
          } else if(null == $get_pag and $x == 1) {
            $paginador .= '<li class="active"><a href="'. $link.'/1/">1</a></li>'; //Página activa
          } else {
            $paginador .= '<li><a href="'. $link . $x .'/">'. $x .'</a></li>';
          }
        }
      } else {
        for($x = $inicio; $x <= $fin; $x++) {
          if(null != $get_pag and $get_pag == $x) {
            $paginador .= '<li class="active"><a href="'. $link . $x .'/">'. $x .'</a></li>'; //Página activa
          } else if(null == $get_pag and $x == 1) {
            $paginador .= '<li class="active"><a href="'. $link.'/1/">1</a></li>'; //Página activa
          } else {
            $paginador .= '<li><a href="'. $link . $x .'/">'. $x .'</a></li>';
          }
        }
      }
    }

    return $paginador;
  }

  //------------------------------------------------

  const PAGER_CONFIG = array(
    'anterior' => 'Anterior', # Texto del botón 'anterior'
    'siguiente' => 'Siguiente', # Texto del botón 'siguiente'
    'i' => 4, # Cantidad de números máximos por la izquierda
    'd' => 4 # Cantidad de números máximos por la derecha
  );

  /**
    * Devuelve un paginador numérico de la forma [<< anterior - 1 - 2 - 3 - *4* - 5 - 6 - 7 - siguiente >>]
    * Para que este funcione, $get_pag debe ser el valor que contenga una ruta numérica, en este caso la que pase de números en la URL
    *
    * @param string $link: Formato de URL de continuidad para el paginador, ejemplo /controlador/otras/rutas/
    * @param int $total_pags: Número de páginas totales
    * @param int/null $get_pag: Variable que contiene la página actual, tiene que provenir de $this->route probablemente
    * @param string $extra_css: Css extra anidado en la etiqueta class
    * @param array $conf: Arreglo con la forma de la constante PAGER_CONFIG, contiene configuración
    *
    * @return string con el paginador funcional
  */
  final public static function pager(string $link, int $total_pags, $get_pag, string $extra_css = '', array $conf = self::PAGER_CONFIG) : string {
    $paginador = '<nav><ul class="pagination '.$extra_css.'">';
        if(null == $get_pag) {
          $paginador .= '<li class="disabled"> <a>'.$conf['anterior'].'</a> </li>';
          $paginador .= self::get_number_pags($link,$total_pags,$get_pag,$conf);
          if($total_pags > 1) {
            $paginador .= '<li> <a href="'. $link .'2/"> '.$conf['siguiente'].'</a> </li>';
          } else {
            $paginador .= '<li class="disabled"> <a> '.$conf['siguiente'].' </a> </li>';
          }
        } else {
          if($total_pags > 1) {
            if($get_pag > 1 and $get_pag < $total_pags) {
              $paginador .= '<li> <a href="'. $link . ($get_pag - 1) .'/">'.$conf['anterior'].'</a> </li>'; //Atrás
              $paginador .= self::get_number_pags($link,$total_pags,$get_pag,$conf);
              $paginador .= '<li> <a href="'. $link . ($get_pag + 1) .'/"> '.$conf['siguiente'].' </a> </li>'; //Siguiente
            } else if($get_pag == 1) {
              $paginador .= '<li class="disabled"> <a>'.$conf['anterior'].'</a> </li>'; //Atrás
              $paginador .= self::get_number_pags($link,$total_pags,$get_pag,$conf);
              $paginador .= '<li> <a href="'. $link . ($get_pag + 1) .'"> '.$conf['siguiente'].' </a> </li>'; //Siguiente
            } else {
              $paginador .= '<li> <a href="'. $link . ($get_pag - 1) .'">'.$conf['anterior'].'</a> </li>'; //Atrás
              $paginador .= self::get_number_pags($link,$total_pags,$get_pag,$conf);
              $paginador .= '<li class="disabled"> <a> '.$conf['siguiente'].' </a> </li>'; //Siguiente
            }
          } else {
            $paginador .= '<li class="disabled"> <a> '.$conf['anterior'].'</a> </li>'; //Atrás
            $paginador .= '<li class="disabled"><a>1</a></li>';
            $paginador .= '<li class="disabled"> <a> '.$conf['siguiente'].'  </a> </li>'; //Siguiente
          }
        }
    $paginador .= '</ul></nav>';

    return $paginador;
  }

  //------------------------------------------------

  /**
    * Renderiza una tabla con contenido
    *
    * @param array $th: Todos los TH en orden, el arreglo de la forma array('th1','th2','th3')
    * @param array $values: Arreglo con los valores a recorrer (en forma de matriz), array(
    *                                                                                 array('elemento 1','elemento 2','elemento 3'),
    *                                                                                 array('elemento 1','elemento 2','elemento 3')
    *                                                                                )
    * Cada posición del arreglo principal corresponderá a un TR, y cada posición de los arreglos internos un TD
    *
    * @param string $extra_css: Css extra anidado en la etiqueta class
    *
    * @return string con la tabla formada
  */
  final public static function table(array $th, array $values, string $extra_css = '') : string {
    $thead = '<thead>';
    foreach ($th as $title) {
      $thead .= "<th>$title</th>";
    }
    $thead .= '</thead>';

    $tbody = '<tbody>';
    foreach ($values as $array) {
      $tbody .= '<tr>';
      foreach ($array as $value) {
        $tbody .= "<td>$value</td>";
      }
      $tbody .= '</tr>';
    }
    $tbody .= '</tbody>';

    return "<table class=\"table $extra_css\">
      $thead
      $tbody
    </table>";
  }

  //------------------------------------------------

  /**
    * Despliega un select de bootstrap con los países
    *
    * @param string $name: Nombre para el select, se coloca en name="" e id="id_"
    * @param bool $multi: establecer true si va a ser un select múltiple
    * @param mixed $selected: Puede ser un arreglo con todos los valores que están seleccionados con la forma array('opcion1','opcion2')
    * ó un string simple con el value seleccionado, esto hará que se marque la casilla selected en la opción del select
    * @param string $extra_css: Css extra anidado en la etiqueta class
    *
    * @return string con el select de paises
  */
  final static public function paises(string $name, bool $multi = false, $selected = '', string $extra_css = '') {
    return self::basic_select($name,array(
      'AF' => 'Afghanistan',
    	'AX' => 'Åland Islands',
    	'AL' => 'Albania',
    	'DZ' => 'Algeria',
    	'AS' => 'American Samoa',
    	'AD' => 'Andorra',
    	'AO' => 'Angola',
    	'AI' => 'Anguilla',
    	'AQ' => 'Antarctica',
    	'AG' => 'Antigua and Barbuda',
    	'AR' => 'Argentina',
    	'AM' => 'Armenia',
    	'AW' => 'Aruba',
    	'AU' => 'Australia',
    	'AT' => 'Austria',
    	'AZ' => 'Azerbaijan',
    	'BS' => 'Bahamas',
    	'BH' => 'Bahrain',
    	'BD' => 'Bangladesh',
    	'BB' => 'Barbados',
    	'BY' => 'Belarus',
    	'BE' => 'Belgium',
    	'BZ' => 'Belize',
    	'BJ' => 'Benin',
    	'BM' => 'Bermuda',
    	'BT' => 'Bhutan',
    	'BO' => 'Bolivia, Plurinational State of',
    	'BQ' => 'Bonaire, Sint Eustatius and Saba',
    	'BA' => 'Bosnia and Herzegovina',
    	'BW' => 'Botswana',
    	'BV' => 'Bouvet Island',
    	'BR' => 'Brazil',
    	'IO' => 'British Indian Ocean Territory',
    	'BN' => 'Brunei Darussalam',
    	'BG' => 'Bulgaria',
    	'BF' => 'Burkina Faso',
    	'BI' => 'Burundi',
    	'KH' => 'Cambodia',
    	'CM' => 'Cameroon',
    	'CA' => 'Canada',
    	'CV' => 'Cape Verde',
    	'KY' => 'Cayman Islands',
    	'CF' => 'Central African Republic',
    	'TD' => 'Chad',
    	'CL' => 'Chile',
    	'CN' => 'China',
    	'CX' => 'Christmas Island',
    	'CC' => 'Cocos (Keeling) Islands',
    	'CO' => 'Colombia',
    	'KM' => 'Comoros',
    	'CG' => 'Congo',
    	'CD' => 'Congo, the Democratic Republic of the',
    	'CK' => 'Cook Islands',
    	'CR' => 'Costa Rica',
    	'CI' => 'Côte d\'Ivoire',
    	'HR' => 'Croatia',
    	'CU' => 'Cuba',
    	'CW' => 'Curaçao',
    	'CY' => 'Cyprus',
    	'CZ' => 'Czech Republic',
    	'DK' => 'Denmark',
    	'DJ' => 'Djibouti',
    	'DM' => 'Dominica',
    	'DO' => 'Dominican Republic',
    	'EC' => 'Ecuador',
    	'EG' => 'Egypt',
    	'SV' => 'El Salvador',
    	'GQ' => 'Equatorial Guinea',
    	'ER' => 'Eritrea',
    	'EE' => 'Estonia',
    	'ET' => 'Ethiopia',
    	'FK' => 'Falkland Islands (Malvinas)',
    	'FO' => 'Faroe Islands',
    	'FJ' => 'Fiji',
    	'FI' => 'Finland',
    	'FR' => 'France',
    	'GF' => 'French Guiana',
    	'PF' => 'French Polynesia',
    	'TF' => 'French Southern Territories',
    	'GA' => 'Gabon',
    	'GM' => 'Gambia',
    	'GE' => 'Georgia',
    	'DE' => 'Germany',
    	'GH' => 'Ghana',
    	'GI' => 'Gibraltar',
    	'GR' => 'Greece',
    	'GL' => 'Greenland',
    	'GD' => 'Grenada',
    	'GP' => 'Guadeloupe',
    	'GU' => 'Guam',
    	'GT' => 'Guatemala',
    	'GG' => 'Guernsey',
    	'GN' => 'Guinea',
    	'GW' => 'Guinea-Bissau',
    	'GY' => 'Guyana',
    	'HT' => 'Haiti',
    	'HM' => 'Heard Island and McDonald Islands',
    	'VA' => 'Holy See (Vatican City State)',
    	'HN' => 'Honduras',
    	'HK' => 'Hong Kong',
    	'HU' => 'Hungary',
    	'IS' => 'Iceland',
    	'IN' => 'India',
    	'ID' => 'Indonesia',
    	'IR' => 'Iran, Islamic Republic of',
    	'IQ' => 'Iraq',
    	'IE' => 'Ireland',
    	'IM' => 'Isle of Man',
    	'IL' => 'Israel',
    	'IT' => 'Italy',
    	'JM' => 'Jamaica',
    	'JP' => 'Japan',
    	'JE' => 'Jersey',
    	'JO' => 'Jordan',
    	'KZ' => 'Kazakhstan',
    	'KE' => 'Kenya',
    	'KI' => 'Kiribati',
    	'KP' => 'Korea, Democratic People\'s Republic of',
    	'KR' => 'Korea, Republic of',
    	'KW' => 'Kuwait',
    	'KG' => 'Kyrgyzstan',
    	'LA' => 'Lao People\'s Democratic Republic',
    	'LV' => 'Latvia',
    	'LB' => 'Lebanon',
    	'LS' => 'Lesotho',
    	'LR' => 'Liberia',
    	'LY' => 'Libya',
    	'LI' => 'Liechtenstein',
    	'LT' => 'Lithuania',
    	'LU' => 'Luxembourg',
    	'MO' => 'Macao',
    	'MK' => 'Macedonia, the former Yugoslav Republic of',
    	'MG' => 'Madagascar',
    	'MW' => 'Malawi',
    	'MY' => 'Malaysia',
    	'MV' => 'Maldives',
    	'ML' => 'Mali',
    	'MT' => 'Malta',
    	'MH' => 'Marshall Islands',
    	'MQ' => 'Martinique',
    	'MR' => 'Mauritania',
    	'MU' => 'Mauritius',
    	'YT' => 'Mayotte',
    	'MX' => 'Mexico',
    	'FM' => 'Micronesia, Federated States of',
    	'MD' => 'Moldova, Republic of',
    	'MC' => 'Monaco',
    	'MN' => 'Mongolia',
    	'ME' => 'Montenegro',
    	'MS' => 'Montserrat',
    	'MA' => 'Morocco',
    	'MZ' => 'Mozambique',
    	'MM' => 'Myanmar',
    	'NA' => 'Namibia',
    	'NR' => 'Nauru',
    	'NP' => 'Nepal',
    	'NL' => 'Netherlands',
    	'NC' => 'New Caledonia',
    	'NZ' => 'New Zealand',
    	'NI' => 'Nicaragua',
    	'NE' => 'Niger',
    	'NG' => 'Nigeria',
    	'NU' => 'Niue',
    	'NF' => 'Norfolk Island',
    	'MP' => 'Northern Mariana Islands',
    	'NO' => 'Norway',
    	'OM' => 'Oman',
    	'PK' => 'Pakistan',
    	'PW' => 'Palau',
    	'PS' => 'Palestinian Territory, Occupied',
    	'PA' => 'Panama',
    	'PG' => 'Papua New Guinea',
    	'PY' => 'Paraguay',
    	'PE' => 'Peru',
    	'PH' => 'Philippines',
    	'PN' => 'Pitcairn',
    	'PL' => 'Poland',
    	'PT' => 'Portugal',
    	'PR' => 'Puerto Rico',
    	'QA' => 'Qatar',
    	'RE' => 'Réunion',
    	'RO' => 'Romania',
    	'RU' => 'Russian Federation',
    	'RW' => 'Rwanda',
    	'BL' => 'Saint Barthélemy',
    	'SH' => 'Saint Helena, Ascension and Tristan da Cunha',
    	'KN' => 'Saint Kitts and Nevis',
    	'LC' => 'Saint Lucia',
    	'MF' => 'Saint Martin (French part)',
    	'PM' => 'Saint Pierre and Miquelon',
    	'VC' => 'Saint Vincent and the Grenadines',
    	'WS' => 'Samoa',
    	'SM' => 'San Marino',
    	'ST' => 'Sao Tome and Principe',
    	'SA' => 'Saudi Arabia',
    	'SN' => 'Senegal',
    	'RS' => 'Serbia',
    	'SC' => 'Seychelles',
    	'SL' => 'Sierra Leone',
    	'SG' => 'Singapore',
    	'SX' => 'Sint Maarten (Dutch part)',
    	'SK' => 'Slovakia',
    	'SI' => 'Slovenia',
    	'SB' => 'Solomon Islands',
    	'SO' => 'Somalia',
    	'ZA' => 'South Africa',
    	'GS' => 'South Georgia and the South Sandwich Islands',
    	'SS' => 'South Sudan',
    	'ES' => 'Spain',
    	'LK' => 'Sri Lanka',
    	'SD' => 'Sudan',
    	'SR' => 'Suriname',
    	'SJ' => 'Svalbard and Jan Mayen',
    	'SZ' => 'Swaziland',
    	'SE' => 'Sweden',
    	'CH' => 'Switzerland',
    	'SY' => 'Syrian Arab Republic',
    	'TW' => 'Taiwan, Province of China',
    	'TJ' => 'Tajikistan',
    	'TZ' => 'Tanzania, United Republic of',
    	'TH' => 'Thailand',
    	'TL' => 'Timor-Leste',
    	'TG' => 'Togo',
    	'TK' => 'Tokelau',
    	'TO' => 'Tonga',
    	'TT' => 'Trinidad and Tobago',
    	'TN' => 'Tunisia',
    	'TR' => 'Turkey',
    	'TM' => 'Turkmenistan',
    	'TC' => 'Turks and Caicos Islands',
    	'TV' => 'Tuvalu',
    	'UG' => 'Uganda',
    	'UA' => 'Ukraine',
    	'AE' => 'United Arab Emirates',
    	'GB' => 'United Kingdom',
    	'US' => 'United States',
    	'UM' => 'United States Minor Outlying Islands',
    	'UY' => 'Uruguay',
    	'UZ' => 'Uzbekistan',
    	'VU' => 'Vanuatu',
    	'VE' => 'Venezuela, Bolivarian Republic of',
    	'VN' => 'Viet Nam',
    	'VG' => 'Virgin Islands, British',
    	'VI' => 'Virgin Islands, U.S.',
    	'WF' => 'Wallis and Futuna',
    	'EH' => 'Western Sahara',
    	'YE' => 'Yemen',
    	'ZM' => 'Zambia',
    	'ZW' => 'Zimbabwe'
    ),$multi,$selected,$extra_css);
  }


  //------------------------------------------------

  /**
    * Se obtiene de Twig_Extension y sirve para que cada función esté disponible como etiqueta en twig
    *
    * @return array: Todas las funciones con sus respectivos nombres de acceso en plantillas twig
  */
  public function getFunctions() : array {
    return array(
      new Twig_Function('dropdown', array($this, 'dropdown')),
      new Twig_Function('button_dropdown', array($this, 'button_dropdown')),
      new Twig_Function('button', array($this, 'button')),
      new Twig_Function('basic_input', array($this, 'basic_input')),
      new Twig_Function('basic_select', array($this, 'basic_select')),
      new Twig_Function('checkbox', array($this, 'checkbox')),
      new Twig_Function('radio', array($this, 'radio')),
      new Twig_Function('textarea', array($this, 'textarea')),
      new Twig_Function('alert', array($this, 'alert')),
      new Twig_Function('pager', array($this, 'pager')),
      new Twig_Function('table', array($this, 'table')),
      new Twig_Function('paises', array($this, 'paises'))
    );
  }

  //------------------------------------------------

  /**
    * Identificador único para la extensión de twig
    *
    * @return string: Nombre de la extensión
  */
  public function getName() : string {
    return 'ocrend_framework_helper_bootstrap';
  }

}
