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
    *                                                                           'http://url' => 'nombre',
    *                                                                           'http://url2' => 'nombre2'
    *                                                                           )
    * @param bool $up: Hace que el dropdown se despliegue hacia arriba
    *
    * @return string con el dropdown realizado
  */
  final public static function dropdown(string $name, array $elements, bool $up = false) : string {

    $links = '';
    foreach ($elements as $url => $e) {
      $links .= '<li><a href="'.$url.'">'.$e.'</a></li>';
    }

    return '<div class="'. ($up ? 'dropup' : 'dropdown') . '">
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
    *                                                                           'http://url' => 'nombre',
    *                                                                           'http://url2' => 'nombre2'
    *                                                                           )
    * @param bool $vertical: Hace que el dropdown se despliegue hacia arriba
    *
    * @return string con el dropdown realizado
  */
  final public static function button_dropdown(string $name, array $elements, string $type = 'default', bool $vertical = false) : string {

    $links = '';
    foreach ($elements as $url => $e) {
      $links .= '<li><a href="'.$url.'">'.$e.'</a></li>';
    }

    return '<div class="btn-group '. (!$vertical ?'': 'dropup') .'">
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
      foreach ($options as $value => $name) {
        $opt .= '<option value="'.$value.'"'. (!(in_array($value, $selected)) ?'': 'selected=""') .'>'.$name.'</option>';
      }
    } else {
      foreach ($options as $value => $name) {
        $opt .= '<option value="'.$value.'"'. ($selected != $value ?'': 'selected=""') .'>'.$name.'</option>';
      }
    }

    return '<select name="'.$name.'" id="id_'.$name.'" class="form-control '.$extra_css.'" '.(!$multi ?'': 'multiple').'>
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
    * @param array $conf: Arreglo con la forma de la constante PAGER_CONFIG, contiene configuración
    * @param string $extra_css: Css extra anidado en la etiqueta class
    *
    * @return string con el paginador funcional
  */
  final public static function pager(string $link, int $total_pags, $get_pag, array $conf = self::PAGER_CONFIG, string $extra_css = '') : string {
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
    * Cada posición del arreglo principal correspnderá a un TR, y cada posición de los arreglos internos un TD
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
      foreach ($array as $key => $value) {
        $tbody .= "<td>$key</td>";
      }
      $tbody .= '</tr>';
    }
    $tbody .= '</tbody>';

    return "<table class=\"table $extra_css\">
      $thead
      $tbody
    </table>";
  }

}
