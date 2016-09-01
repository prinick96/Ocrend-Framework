<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

final class Bootstrap {

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
      'Afghanistan' => 'Afghanistan',
      'Albania' => 'Albania',
      'Algeria' => 'Algeria',
      'American Samoa' => 'American Samoa',
      'Andorra' => 'Andorra',
      'Angola' => 'Angola',
      'Anguilla' => 'Anguilla',
      'Antarctica' => 'Antarctica',
      'Antigua and Barbuda' => 'Antigua and Barbuda',
      'Argentina' => 'Argentina',
      'Armenia' => 'Armenia',
      'Aruba' => 'Aruba',
      'Australia' => 'Australia',
      'Austria' => 'Austria',
      'Azerbaijan' => 'Azerbaijan',
      'Bahamas' => 'Bahamas',
      'Bahrain' => 'Bahrain',
      'Bangladesh' => 'Bangladesh',
      'Barbados' => 'Barbados',
      'Belarus' => 'Belarus',
      'Belgium' => 'Belgium',
      'Belize' => 'Belize',
      'Benin' => 'Benin',
      'Bermuda' => 'Bermuda',
      'Bhutan' => 'Bhutan',
      'Bolivia' => 'Bolivia',
      'Bosnia and Herzegovina' => 'Bosnia and Herzegovina',
      'Botswana' => 'Botswana',
      'Bouvet Island' => 'Bouvet Island',
      'Brazil' => 'Brazil',
      'British Indian Ocean Territory' => 'British Indian Ocean Territory',
      'Brunei Darussalam' => 'Brunei Darussalam',
      'Bulgaria' => 'Bulgaria',
      'Burkina Faso' => 'Burkina Faso',
      'Burundi' => 'Burundi',
      'Cambodia' => 'Cambodia',
      'Cameroon' => 'Cameroon',
      'Canada' => 'Canada',
      'Cape Verde' => 'Cape Verde',
      'Cayman Islands' => 'Cayman Islands',
      'Central African Republic' => 'Central African Republic',
      'Chad' => 'Chad',
      'Chile' => 'Chile',
      'China' => 'China',
      'Christmas Island' => 'Christmas Island',
      'Cocos (Keeling) Islands' => 'Cocos (Keeling) Islands',
      'Colombia' => 'Colombia',
      'Comoros' => 'Comoros',
      'Congo' => 'Congo',
      'Congo, The Democratic Republic of The' => 'Congo, The Democratic Republic of The',
      'Cook Islands' => 'Cook Islands',
      'Costa Rica' => 'Costa Rica',
      'Cote D\'ivoire' => 'Cote D\'ivoire',
      'Croatia' => 'Croatia',
      'Cuba' => 'Cuba',
      'Cyprus' => 'Cyprus',
      'Czech Republic' => 'Czech Republic',
      'Denmark' => 'Denmark',
      'Djibouti' => 'Djibouti',
      'Dominica' => 'Dominica',
      'Dominican Republic' => 'Dominican Republic',
      'Ecuador' => 'Ecuador',
      'Egypt' => 'Egypt',
      'El Salvador' => 'El Salvador',
      'Equatorial Guinea' => 'Equatorial Guinea',
      'Eritrea' => 'Eritrea',
      'Estonia' => 'Estonia',
      'Ethiopia' => 'Ethiopia',
      'Falkland Islands (Malvinas)' => 'Falkland Islands (Malvinas)',
      'Faroe Islands' => 'Faroe Islands',
      'Fiji' => 'Fiji',
      'Finland' => 'Finland',
      'France' => 'France',
      'French Guiana' => 'French Guiana',
      'French Polynesia' => 'French Polynesia',
      'French Southern Territories' => 'French Southern Territories',
      'Gabon' => 'Gabon',
      'Gambia' => 'Gambia',
      'Georgia' => 'Georgia',
      'Germany' => 'Germany',
      'Ghana' => 'Ghana',
      'Gibraltar' => 'Gibraltar',
      'Greece' => 'Greece',
      'Greenland' => 'Greenland',
      'Grenada' => 'Grenada',
      'Guadeloupe' => 'Guadeloupe',
      'Guam' => 'Guam',
      'Guatemala' => 'Guatemala',
      'Guinea' => 'Guinea',
      'Guinea-bissau' => 'Guinea-bissau',
      'Guyana' => 'Guyana',
      'Haiti' => 'Haiti',
      'Heard Island and Mcdonald Islands' => 'Heard Island and Mcdonald Islands',
      'Holy See (Vatican City State)' => 'Holy See (Vatican City State)',
      'Honduras' => 'Honduras',
      'Hong Kong' => 'Hong Kong',
      'Hungary' => 'Hungary',
      'Iceland' => 'Iceland',
      'India' => 'India',
      'Indonesia' => 'Indonesia',
      'Iran, Islamic Republic of' => 'Iran, Islamic Republic of',
      'Iraq' => 'Iraq',
      'Ireland' => 'Ireland',
      'Israel' => 'Israel',
      'Italy' => 'Italy',
      'Jamaica' => 'Jamaica',
      'Japan' => 'Japan',
      'Jordan' => 'Jordan',
      'Kazakhstan' => 'Kazakhstan',
      'Kenya' => 'Kenya',
      'Kiribati' => 'Kiribati',
      'Korea, Democratic People\'s Republic of' => 'Korea, Democratic People\'s Republic of',
      'Korea, Republic of' => 'Korea, Republic of',
      'Kuwait' => 'Kuwait',
      'Kyrgyzstan' => 'Kyrgyzstan',
      'Lao People\'s Democratic Republic' => 'Lao People\'s Democratic Republic',
      'Latvia' => 'Latvia',
      'Lebanon' => 'Lebanon',
      'Lesotho' => 'Lesotho',
      'Liberia' => 'Liberia',
      'Libyan Arab Jamahiriya' => 'Libyan Arab Jamahiriya',
      'Liechtenstein' => 'Liechtenstein',
      'Lithuania' => 'Lithuania',
      'Luxembourg' => 'Luxembourg',
      'Macao' => 'Macao',
      'Macedonia, The Former Yugoslav Republic of' => 'Macedonia, The Former Yugoslav Republic of',
      'Madagascar' => 'Madagascar',
      'Malawi' => 'Malawi',
      'Malaysia' => 'Malaysia',
      'Maldives' => 'Maldives',
      'Mali' => 'Mali',
      'Malta' => 'Malta',
      'Marshall Islands' => 'Marshall Islands',
      'Martinique' => 'Martinique',
      'Mauritania' => 'Mauritania',
      'Mauritius' => 'Mauritius',
      'Mayotte' => 'Mayotte',
      'Mexico' => 'Mexico',
      'Micronesia, Federated States of' => 'Micronesia, Federated States of',
      'Moldova, Republic of' => 'Moldova, Republic of',
      'Monaco' => 'Monaco',
      'Mongolia' => 'Mongolia',
      'Montserrat' => 'Montserrat',
      'Morocco' => 'Morocco',
      'Mozambique' => 'Mozambique',
      'Myanmar' => 'Myanmar',
      'Namibia' => 'Namibia',
      'Nauru' => 'Nauru',
      'Nepal' => 'Nepal',
      'Netherlands' => 'Netherlands',
      'Netherlands Antilles' => 'Netherlands Antilles',
      'New Caledonia' => 'New Caledonia',
      'New Zealand' => 'New Zealand',
      'Nicaragua' => 'Nicaragua',
      'Niger' => 'Niger',
      'Nigeria' => 'Nigeria',
      'Niue' => 'Niue',
      'Norfolk Island' => 'Norfolk Island',
      'Northern Mariana Islands' => 'Northern Mariana Islands',
      'Norway' => 'Norway',
      'Oman' => 'Oman',
      'Pakistan' => 'Pakistan',
      'Palau' => 'Palau',
      'Palestinian Territory, Occupied' => 'Palestinian Territory, Occupied',
      'Panama' => 'Panama',
      'Papua New Guinea' => 'Papua New Guinea',
      'Paraguay' => 'Paraguay',
      'Peru' => 'Peru',
      'Philippines' => 'Philippines',
      'Pitcairn' => 'Pitcairn',
      'Poland' => 'Poland',
      'Portugal' => 'Portugal',
      'Puerto Rico' => 'Puerto Rico',
      'Qatar' => 'Qatar',
      'Reunion' => 'Reunion',
      'Romania' => 'Romania',
      'Russian Federation' => 'Russian Federation',
      'Rwanda' => 'Rwanda',
      'Saint Helena' => 'Saint Helena',
      'Saint Kitts and Nevis' => 'Saint Kitts and Nevis',
      'Saint Lucia' => 'Saint Lucia',
      'Saint Pierre and Miquelon' => 'Saint Pierre and Miquelon',
      'Saint Vincent and The Grenadines' => 'Saint Vincent and The Grenadines',
      'Samoa' => 'Samoa',
      'San Marino' => 'San Marino',
      'Sao Tome and Principe' => 'Sao Tome and Principe',
      'Saudi Arabia' => 'Saudi Arabia',
      'Senegal' => 'Senegal',
      'Serbia and Montenegro' => 'Serbia and Montenegro',
      'Seychelles' => 'Seychelles',
      'Sierra Leone' => 'Sierra Leone',
      'Singapore' => 'Singapore',
      'Slovakia' => 'Slovakia',
      'Slovenia' => 'Slovenia',
      'Solomon Islands' => 'Solomon Islands',
      'Somalia' => 'Somalia',
      'South Africa' => 'South Africa',
      'South Georgia and The South Sandwich Islands' => 'South Georgia and The South Sandwich Islands',
      'Spain' => 'Spain',
      'Sri Lanka' => 'Sri Lanka',
      'Sudan' => 'Sudan',
      'Suriname' => 'Suriname',
      'Svalbard and Jan Mayen' => 'Svalbard and Jan Mayen',
      'Swaziland' => 'Swaziland',
      'Sweden' => 'Sweden',
      'Switzerland' => 'Switzerland',
      'Syrian Arab Republic' => 'Syrian Arab Republic',
      'Taiwan, Province of China' => 'Taiwan, Province of China',
      'Tajikistan' => 'Tajikistan',
      'Tanzania, United Republic of' => 'Tanzania, United Republic of',
      'Thailand' => 'Thailand',
      'Timor-leste' => 'Timor-leste',
      'Togo' => 'Togo',
      'Tokelau' => 'Tokelau',
      'Tonga' => 'Tonga',
      'Trinidad and Tobago' => 'Trinidad and Tobago',
      'Tunisia' => 'Tunisia',
      'Turkey' => 'Turkey',
      'Turkmenistan' => 'Turkmenistan',
      'Turks and Caicos Islands' => 'Turks and Caicos Islands',
      'Tuvalu' => 'Tuvalu',
      'Uganda' => 'Uganda',
      'Ukraine' => 'Ukraine',
      'United Arab Emirates' => 'United Arab Emirates',
      'United Kingdom' => 'United Kingdom',
      'United States' => 'United States',
      'United States Minor Outlying Islands' => 'United States Minor Outlying Islands',
      'Uruguay' => 'Uruguay',
      'Uzbekistan' => 'Uzbekistan',
      'Vanuatu' => 'Vanuatu',
      'Venezuela' => 'Venezuela',
      'Viet Nam' => 'Viet Nam',
      'Virgin Islands, British' => 'Virgin Islands, British',
      'Virgin Islands, U.S.' => 'Virgin Islands, U.S.',
      'Wallis and Futuna' => 'Wallis and Futuna',
      'Western Sahara' => 'Western Sahara',
      'Yemen' => 'Yemen',
      'Zambia' => 'Zambia',
      'Zimbabwe' => 'Zimbabwe'
    ),$multi,$selected,$extra_css);
  }


}
