<?php

/**
  * Ocrend Framework - MVC Architecture for Web Applications
  * PHP Version 7
  * @package Ocrend Framework
  * @version v1.2
  * @author Brayan Narváez (Prinick) <prinick@ocrend.com> <youtube.com/user/prinick96>
  * @copyright 2017 - Ocrend Software
  * @license	http://opensource.org/licenses/MIT	MIT License
  * @link http://framework.ocrend.com/
  *
  * Generador de código por consola en PHP
  *
*/

//------------------------------------------------
# Conexión con el framework
define('IS_API',false);
define('INDEX_DIR',true);
define('GENERATOR',true);
require('core/config.php');

//------------------------------------------------

# Cantidad de argumentos incorrecta
if(isset($argc) && $argc < 2) {
  echo "ERROR: La forma correcta es:\nphp gen.php [opciones] [modulo] \n\nPara mayor informacion:\nphp gen.php -ayuda";
  exit(1);
# Ejecución del generador
} else if(isset($argc) && $argc >= 2) {
  new OCRENDGenerator($argv,$argc);
# Salida de emergencia por si no hay acceso desde consola
} else {
  header('location: index.php');
}

//------------------------------------------------

# Motor del generador
class OCRENDGenerator {

  # Variables
  private $db = null;
  private $args = array();
  private $count_args = 0;
  private $modelName = null;
  private $controllerName = null;
  private $viewName = null;
  # Presencia de modelo
  private $model = false;
  # Presencia de controlador
  private $controller = false;
  # Presencia de vista
  private $view = false;
  # Presencia de la api e indice de $api_http
  private $api = -1;
  # Posibles peticiones HTTP de la api
  private $api_http = ['get','post','put','delete','map'];
  # Presencia de tabla en la base de datos
  private $database = false;
  /**
    * Campos de la tabla en la base de datos
    * array('campo' => 'tipo(longitud)')
  */
  private $fields = array();
  # Nombre de la tabla
  private $table_name = null;

  # Constantes
  const TPL_FOLDER = USE_TWIG_TEMPLATE_ENGINE ? 'twig/' : 'plates/';
  const R_MODELS = './core/models/';
  const R_CONTROLLERS = './core/controllers/';
  const R_VIEWS = './templates/' . self::TPL_FOLDER;
  const TEMPLATE_EXTENSIONS = ['phtml','twig'];

  /**
    * Devuelve un string con el contenido de un archivo
    *
    * @param string $dir: Directorio del archivo a leer
    *
    * @return string con contenido del archivo
  */
  private function readFile(string $dir) : string {
    $lines = '';
    $f = new SplFileObject($dir);
    while (!$f->eof()) {
        $lines .= $f->fgets();
    }
    return (string) $lines;
  }

  /**
    * Escribe un string completo en un archivo, si este no existe lo crea
    *
    * @param string $dir: Directorio del archivo escribir/crear
    * @param string $content: Contenido a escribir
    *
    * @return catidad de bytes escritos en el archivo
  */
  private function writeFile(string $dir, string $content) : int {
    $f = new SplFileObject($dir,'w');
    return (int) $f->fwrite($content);
  }

  /**
    * Escribe un string al final, en un archivo existente
    *
    * @param string $dir: Directorio del archivo sobre el cual se va a escribir
    * @param string $content: Contenido a escribir
    *
    * @return catidad de bytes escritos en el archivo
  */
  private function writeInFile(string $dir, string $content) : int {
    $f = new SplFileObject($dir,'a+');
    echo 'Se ha escrito en ' . $dir . "\n";
    return (int) $f->fwrite("\n\n" . $content);
  }

  /**
    * Verifica si existe un archivo en el sistema
    *
    * @param string $file: Ruta del archivo
    *
    * @return bool : true si existe y muestra un mensaje, false si no existe
  */
  private function fileExist(string $file) : bool {
    if(file_exists($file)) {
      echo 'El archivo ' , $file , ' ya existe y no fue sobreescrito por seguridad.' , "\n";
      return true;
    }

    return false;
  }

  /**
    * Reemplaza las keys de las plantillas por el contenido real
    *
    * @param string $content: Contenido
    * @param string $method: string del método [get,post,put,delete,map]
    * @param string $api: Url con la forma modulo/editar
    *
    * @return string : Devuelve el contenido formateado reemplazando todas las llaves por su equivalente
  */
  private function replaceKeys(string $content, string $method, string $api) : string {
    return str_replace('{{view}}',$this->viewName,
          str_replace('{{controller}}',$this->controllerName,
          str_replace('{{model}}',$this->modelName,
          str_replace('{{api_rest}}',$api,
          str_replace('{{method}}',$method,$content)))));
  }

  /**
    * Se encarga de generar un archivo PHP, JS o HTML (twig/phtml)
    *
    * @param int $t : Tipo de archivo a crear [0:modelo, 1:controlador, 2:vista, 3:javascript]
    * @param string $tpl : Ruta de la plantilla que usará el generador
    * @param string $method: string del método [get,post,put,delete,map]
    * @param string $api: Url con la forma modulo/editar
    *
  */
  private function generateFile(int $t,string $tpl, string $method = '', string $api = '') {
    $type = [
      self::R_MODELS . $this->modelName . '.php', # modelo
      self::R_CONTROLLERS . $this->controllerName . '.php', # controlador
      self::R_VIEWS . $this->viewName . '/' . $this->viewName . '.' . self::TEMPLATE_EXTENSIONS[(int) USE_TWIG_TEMPLATE_ENGINE], # vista
      './views/app/js/' . $this->viewName . '/' . $this->viewName . '.js', # javascript
      './views/app/js/' . $this->viewName . '/crear.js', # javascript CREAR para crud
      './views/app/js/' . $this->viewName . '/editar.js', # javascript EDITAR para crud
    ];
    # Creamos el directorio en caso de que sea un .js
    if(!is_dir('./views/app/js/' . $this->viewName . '/') && in_array($t,[3,4,5])) {
      mkdir('./views/app/js/' . $this->viewName . '/');
    }

    # Creamos el directorio en caso de que sea un .twig/.phtml
    if(!is_dir(self::R_VIEWS . $this->viewName . '/') && 2 == $t) {
      mkdir(self::R_VIEWS . $this->viewName . '/');
    }

    # Obtenemos la ruta completa del archivo
    $fileName = $type[$t];
    # Si no existe el archivo, se crea
    if(!$this->fileExist($fileName)) {
      $size = $this->writeFile($fileName,$this->replaceKeys($this->readFile($tpl),$method,$api));
      echo 'Se ha creado ' , $fileName , ' con un peso de ', number_format($size,0,',','.') , " bytes.\n";
    }
  }

  /**
    * Se encarga de escribir sobre un fichero de api rest
    *
    * @param string $mode : String con la forma '/editar'
    * @param string $class_method : Método del modelo a ser llamado
    *
  */
  private function writeApi(string $mode = '', string $class_method = 'foo') {
    if($this->api > -1) {
      $varname = strtolower($this->modelName[0]);
      $method = ['get(','post(','put(','delete(',"map(['GET', 'POST'], "];
      $php_global_var = ['$_GET','$_POST','array()','array()','$_GET || $_POST'];
      # Por más que resulte tentativo, no identar este pedazo de string
      $content = '/**
  * '.$this->modelName.' acceso rest
  * @return Devuelve un json con información acerca del éxito o posibles errores.
*/
$app->'.$method[$this->api].'\'/'. $this->viewName . $mode .'\',function($request, $response){

  $'.$varname.' = new '.$this->modelName.';
  $response->withJson($'.$varname.'->'.$class_method.'('. $php_global_var[$this->api] .'));

  return $response;
});';
    # Ok continúa
      $fileName = './api/http/' . $this->api_http[$this->api] . '.php';
      $this->writeInFile($fileName,$content);
    }
  }

  /**
    * Encargado de mostrar la ayuda en consola
  */
  private function showHelp() {
    echo "\n======================= AYUDA =======================\n\n";
    echo "La siguiente lista representa las combinaciones posibles.\n";
    echo "IMPORTANTE: Los campos entre [] para las tablas en la base de datos no pueden contener espacios.\n\n\n";

    echo "Modelo:\n- php gen.php m Nombre\n\n";
    echo "Modelo y Tabla en Base de Datos:\n- php gen.php m Nombre b nombre_tabla [campo1:tipo[longitud],campo2:tipo[longitud],...]\n\n";
    echo "Modelo y Peticion GET API Rest:\n- php gen.php ma:get Nombre\n\n";
    echo "Modelo y Peticion POST API Rest:\n- php gen.php ma:post Nombre\n\n";
    echo "Modelo y Peticion PUT API Rest:\n- php gen.php ma:put Nombre\n\n";
    echo "Modelo y Peticion DELETE API Rest:\n- php gen.php ma:delete Nombre\n\n";
    echo "Modelo y Peticion MAP API Rest:\n- php gen.php ma:map Nombre\n\n";
    echo "Modelo, Tabla en Base de Datos y Peticion GET API Rest:\n- php gen.php ma:get Nombre b nombre_tabla [campo1:tipo[longitud],campo2:tipo[longitud],...]\n\n";
    echo "Modelo, Tabla en Base de Datos y Peticion POST API Rest:\n- php gen.php ma:post Nombre b nombre_tabla [campo1:tipo[longitud],campo2:tipo[longitud],...]\n\n";
    echo "Modelo, Tabla en Base de Datos y Peticion PUT API Rest:\n- php gen.php ma:put Nombre b nombre_tabla [campo1:tipo[longitud],campo2:tipo[longitud],...]\n\n";
    echo "Modelo, Tabla en Base de Datos y Peticion DELETE API Rest:\n- php gen.php ma:delete Nombre b nombre_tabla [campo1:tipo[longitud],campo2:tipo[longitud],...]\n\n";
    echo "Modelo, Tabla en Base de Datos y Peticion MAP API Rest:\n- php gen.php ma:map Nombre b nombre_tabla [campo1:tipo[longitud],campo2:tipo[longitud],...]\n\n\n";

    echo "Modelo y Controlador:\n- php gen.php mc Nombre\n\n";
    echo "Modelo, Controlador y Tabla en Base de Datos:\n- php gen.php mc Nombre b nombre_tabla [campo1:tipo[longitud],campo2:tipo[longitud],...]\n\n";
    echo "Modelo, Controlador y Peticion GET API Rest:\n- php gen.php mca:get Nombre\n\n";
    echo "Modelo, Controlador y Peticion POST API Rest:\n- php gen.php mca:post Nombre\n\n";
    echo "Modelo, Controlador y Peticion PUT API Rest:\n- php gen.php mca:put Nombre\n\n";
    echo "Modelo, Controlador y Peticion DELETE API Rest:\n- php gen.php mca:delete Nombre\n\n";
    echo "Modelo, Controlador y Peticion MAP API Rest:\n- php gen.php mca:map Nombre\n\n";
    echo "Modelo, Controlador, Tabla en Base de Datos y Peticion GET API Rest:\n- php gen.php mca:get Nombre b nombre_tabla [campo1:tipo[longitud],campo2:tipo[longitud],...]\n\n";
    echo "Modelo, Controlador, Tabla en Base de Datos y Peticion POST API Rest:\n- php gen.php mca:post Nombre b nombre_tabla [campo1:tipo[longitud],campo2:tipo[longitud],...]\n\n";
    echo "Modelo, Controlador, Tabla en Base de Datos y Peticion PUT API Rest:\n- php gen.php mca:put Nombre b nombre_tabla [campo1:tipo[longitud],campo2:tipo[longitud],...]\n\n";
    echo "Modelo, Controlador, Tabla en Base de Datos y Peticion DELETE API Rest:\n- php gen.php mca:delete Nombre b nombre_tabla [campo1:tipo[longitud],campo2:tipo[longitud],...]\n\n";
    echo "Modelo, Controlador, Tabla en Base de Datos y Peticion MAP API Rest:\n- php gen.php mca:map Nombre b nombre_tabla [campo1:tipo[longitud],campo2:tipo[longitud],...]\n\n\n";

    echo "Modelo, Vista y Controlador:\n- php gen.php mvc Nombre\n\n";
    echo "Modelo, Vista, Controlador y Tabla en Base de Datos:\n- php gen.php mvc Nombre b nombre_tabla [campo1:tipo[longitud],campo2:tipo[longitud],...]\n\n";
    echo "Modelo, Vista, Controlador y Peticion GET API Rest:\n- php gen.php mvca:get Nombre\n\n";
    echo "Modelo, Vista, Controlador y Peticion POST API Rest:\n- php gen.php mvca:post Nombre\n\n";
    echo "Modelo, Vista, Controlador y Peticion PUT API Rest:\n- php gen.php mvca:put Nombre\n\n";
    echo "Modelo, Vista, Controlador y Peticion DELETE API Rest:\n- php gen.php mvca:delete Nombre\n\n";
    echo "Modelo, Vista, Controlador y Peticion MAP API Rest:\n- php gen.php mvca:map Nombre\n\n";
    echo "Modelo, Vista, Controlador, Tabla en Base de Datos y Peticion GET API Rest:\n- php gen.php mvca:get Nombre b nombre_tabla [campo1:tipo[longitud],campo2:tipo[longitud],...]\n\n";
    echo "Modelo, Vista, Controlador, Tabla en Base de Datos y Peticion POST API Rest:\n- php gen.php mvca:post Nombre b nombre_tabla [campo1:tipo[longitud],campo2:tipo[longitud],...]\n\n";
    echo "Modelo, Vista, Controlador, Tabla en Base de Datos y Peticion PUT API Rest:\n- php gen.php mvca:put Nombre b nombre_tabla [campo1:tipo[longitud],campo2:tipo[longitud],...]\n\n";
    echo "Modelo, Vista, Controlador, Tabla en Base de Datos y Peticion DELETE API Rest:\n- php gen.php mvca:delete Nombre b nombre_tabla [campo1:tipo[longitud],campo2:tipo[longitud],...]\n\n";
    echo "Modelo, Vista, Controlador, Tabla en Base de Datos y Peticion MAP API Rest:\n- php gen.php mvca:map Nombre b nombre_tabla [campo1:tipo[longitud],campo2:tipo[longitud],...]\n\n\n";

    echo "Modelo y Vista:\n- php gen.php mv Nombre\n\n";
    echo "Modelo, Vista y Tabla en Base de Datos:\n- php gen.php mv Nombre b nombre_tabla [campo1:tipo[longitud],campo2:tipo[longitud],...]\n\n";
    echo "Modelo, Vista y Peticion GET API Rest:\n- php gen.php mva:get Nombre\n\n";
    echo "Modelo, Vista y Peticion POST API Rest:\n- php gen.php mva:post Nombre\n\n";
    echo "Modelo, Vista y Peticion PUT API Rest:\n- php gen.php mva:put Nombre\n\n";
    echo "Modelo, Vista y Peticion DELETE API Rest:\n- php gen.php mva:delete Nombre\n\n";
    echo "Modelo, Vista y Peticion MAP API Rest:\n- php gen.php mva:map Nombre\n\n";
    echo "Modelo, Vista, Tabla en Base de Datos y Peticion GET API Rest:\n- php gen.php mva:get Nombre b nombre_tabla [campo1:tipo[longitud],campo2:tipo[longitud],...]\n\n";
    echo "Modelo, Vista, Tabla en Base de Datos y Peticion POST API Rest:\n- php gen.php mva:post Nombre b nombre_tabla [campo1:tipo[longitud],campo2:tipo[longitud],...]\n\n";
    echo "Modelo, Vista, Tabla en Base de Datos y Peticion PUT API Rest:\n- php gen.php mva:put Nombre b nombre_tabla [campo1:tipo[longitud],campo2:tipo[longitud],...]\n\n";
    echo "Modelo, Vista, Tabla en Base de Datos y Peticion DELETE API Rest:\n- php gen.php mva:delete Nombre b nombre_tabla [campo1:tipo[longitud],campo2:tipo[longitud],...]\n\n";
    echo "Modelo, Vista, Tabla en Base de Datos y Peticion MAP API Rest:\n- php gen.php mva:map Nombre b nombre_tabla [campo1:tipo[longitud],campo2:tipo[longitud],...]\n\n\n";

    echo "Controlador:\n- php gen.php c Nombre\n\n";
    echo "Vista:\n- php gen.php v Nombre\n\n";
    echo "Controlador y Vista:\n- php gen.php cv Nombre\n\n";

    echo "Tabla en la Base de Datos:\n- php gen.php b nombre_tabla [campo1:tipo[longitud],campo2:tipo[longitud],...]\n\n";

    echo "CRUD (crear, listar, editar, eliminar) y Tabla en Base de Datos:\n- php gen.php crud Nombre b nombre_tabla [campo1:tipo[longitud],campo2:tipo[longitud],...]\n\n";

    echo "\nPara mayor info visitar http://framework.ocrend.com/generador/\n";
  }

  /**
    * Genera un CRUD completo que se conecta a la base de datos y muestra información en pantalla.
  */
  private function generateCrud() {

    # Si hay que crear la base de datos se realiza la consulta previa
    if($this->database) {
      $this->checkSQLSyntax();
      $this->generateSQL();
    }

    # Construimos el controlador
    $fileName = self::R_CONTROLLERS . $this->controllerName . '.php';
    # Si no existe el archivo, se crea
    if(!$this->fileExist($fileName)) {
      $content = $this->replaceKeys($this->readFile('./generator/crud/c.g'),'','');
      $size = $this->writeFile($fileName,str_replace('{{variable}}',$this->controllerName[0],$content));
      echo 'Se ha creado ' , $fileName , ' con un peso de ', number_format($size,0,',','.') , " bytes.\n";
    }

    # Construimos el modelo
    $fileName = self::R_MODELS . $this->modelName . '.php';
    if(!$this->fileExist($fileName)) {
      $content = $this->replaceKeys($this->readFile('./generator/crud/m.g'),'','');

      # Nombre de la tabla
      $content = str_replace('{{table_name}}',$this->table_name,$content);

      # Errores (No identar más)
      $content = str_replace('{{errores_php}}','
    if(!Func::all_full($data)) {
      throw new Exception(\'Todos los campos son necesarios.\');
    }',     $content);

      # Creación query
      $query = '$i = array(' . "\n";
      $i = 1; $sizeof = sizeof($this->fields);
      foreach ($this->fields as $campo => $tipo) {
        $campo = trim($campo);
        if($campo != 'id') {
          $query .=  "\t\t\t" . '\''.$campo.'\' => $data[\''.$campo.'\']';
          # Poner coma
          if($i < $sizeof) {
            $query .= ',';
          } $i++;
          $query .= "\n";
        }
      }
      $query .= "\t\t);\n\t\t";
      $create_query = $query . '$this->db->insert(\''.$this->table_name.'\',$i);';
      $content = str_replace('{{crear_php}}',$create_query,$content);

      # Edición query
      $edit_query = $query . '$this->db->update(\''.$this->table_name.'\',$i,"id=\'$this->id\'",\'LIMIT 1\');';
      $content = str_replace('{{editar_php}}',$edit_query,$content);
      $size = $this->writeFile($fileName,$content);
      echo 'Se ha creado ' , $fileName , ' con un peso de ', number_format($size,0,',','.') , " bytes.\n";
    }

    # Construimos la API (modulo/editar y modulo/crear)
    $this->writeApi('/crear','crear');
    $this->writeApi('/editar','editar');

    # Construimos los JavaScript
    $this->generateFile(4,'./generator/js.g',strtoupper($this->api_http[$this->api]),$this->viewName . '/crear');
    $this->generateFile(5,'./generator/js.g',strtoupper($this->api_http[$this->api]),$this->viewName . '/editar');

    # Creo la ruta para las vistas
    $VIEW_PATH = self::R_VIEWS . $this->viewName . '/';
    if(!is_dir($VIEW_PATH)) {
      mkdir($VIEW_PATH);
    }

    # Construimos la vista principal (tabla con listado)
    $fileName = self::R_VIEWS . $this->viewName . '/' . $this->viewName . '.' . self::TEMPLATE_EXTENSIONS[(int) USE_TWIG_TEMPLATE_ENGINE];
    if(!$this->fileExist($fileName)) {
      $content = $this->replaceKeys($this->readFile('./generator/crud/'.self::TPL_FOLDER.'vista.g'),'','');
      # Construcción del THEAD
      $thead_content = "<tr>";
      foreach ($this->fields as $campo => $tipo) {
        $campo = trim($campo);
        if($campo != 'id') {
          $thead_content .= "<th>".ucwords($campo)."</th>";
        }
      }
      $thead_content .= "<th>Acciones</th></tr>";

      # Construcción del TBODY
      if(USE_TWIG_TEMPLATE_ENGINE) { # Construcción del bucle en el tpl
        $tbody_content = '{% for d in data if false != data %}';
        $tbody_id_syntax = '{{ d.id }}';
      } else {
        $tbody_content = '<?php foreach(false !== $data ? $data : array() as $d): ?>';
        $tbody_id_syntax = '<?= $d[\'id\'] ?>';
      }
      $tbody_content .= "\r\n<tr>";

      # Creando el contenido interno del foreach/for en el TPL
      foreach ($this->fields as $campo => $tipo) {
        $campo = trim($campo);
        if($campo != 'id') {
          $tbody_content .= "\r\n<td>";
          if(USE_TWIG_TEMPLATE_ENGINE) {
            $tbody_content .= '{{ d.' . $campo . ' }}';
          } else {
            $tbody_content .= '<?= $d[\'' . $campo . '\'] ?>';
          }
          $tbody_content .= "</td>";
        }
      }

      # Obtengo las acciones
      $tbody_content .= str_replace('{{view}}',$this->viewName,
                        str_replace('{{id}}',$tbody_id_syntax,$this->readFile('./generator/crud/resources/actions.g')));

      # Coloco el modal del delete
      $tbody_content .= str_replace('{{view}}',$this->viewName,
                        str_replace('{{id}}',$tbody_id_syntax,$this->readFile('./generator/crud/resources/deletemodal.g')));

      $tbody_content .= "</tr>";
      if(USE_TWIG_TEMPLATE_ENGINE) { # Fin del bucle en el tpl
        $tbody_content .= "\r\n" . '{% endfor %}';
      } else {
        $tbody_content .= "\r\n" . '<?php endforeach ?>';
      }

      # Construcción de la tabla
      $table_content = $this->readFile('./generator/crud/resources/tables.g');
      $table_content = str_replace('{{thead}}',$thead_content,$table_content);
      $table_content = str_replace('{{tbody}}',$tbody_content,$table_content);
      $size = $this->writeFile($fileName,str_replace('{{tables}}',str_replace("\r\n","\r\n\t\t\t\t\t\t",$table_content),$content));
      echo 'Se ha creado ' , $fileName , ' con un peso de ', number_format($size,0,',','.') , " bytes.\n";
    }

    # Construimos la vista de creación
    $fileName = self::R_VIEWS . $this->viewName . '/crear.' . self::TEMPLATE_EXTENSIONS[(int) USE_TWIG_TEMPLATE_ENGINE];
    if(!$this->fileExist($fileName)) {
      $content = $this->replaceKeys($this->readFile('./generator/crud/'.self::TPL_FOLDER.'crear.g'),'','');
      # Obtener los inputs
      $content_inputs = $this->readFile('./generator/crud/resources/inputs.g');
      $inputs_creation = '';
      $inputs_edition = '';
      foreach ($this->fields as $campo => $tipo) {
        $campo = trim($campo);
        if($campo != 'id') {
          # Inputs para la creación
          $inputs_creation .= "\r\n" . str_replace('{{name}}',$campo,
                    str_replace('{{etiqueta}}',ucwords($campo),
                    str_replace('{{value}}','',$content_inputs)));
          # Inputs en la edición
          $inputs_edition .= "\r\n" . str_replace('{{name}}',$campo,
                    str_replace('{{etiqueta}}',ucwords($campo),
                    str_replace('{{value}}',
                      USE_TWIG_TEMPLATE_ENGINE ? ('{{ data.'.$campo.' }}') : ('<?= $data[\''.$campo.'\'] ?>')
                    ,$content_inputs)));
        }
      }
      $size = $this->writeFile($fileName,str_replace('{{inputs}}',str_replace("\r\n","\r\n\t\t\t\t\t\t\t",$inputs_creation),$content));
      echo 'Se ha creado ' , $fileName , ' con un peso de ', number_format($size,0,',','.') , " bytes.\n";
    }

    # Construimos la vista de edición
    $fileName = self::R_VIEWS . $this->viewName . '/editar.' . self::TEMPLATE_EXTENSIONS[(int) USE_TWIG_TEMPLATE_ENGINE];
    if(!$this->fileExist($fileName)) {
      $content = $this->replaceKeys($this->readFile('./generator/crud/'.self::TPL_FOLDER.'editar.g'),'','');
      $size = $this->writeFile($fileName,str_replace('{{inputs}}',str_replace("\r\n","\r\n\t\t\t\t\t\t\t",$inputs_edition),$content));
      echo 'Se ha creado ' , $fileName , ' con un peso de ', number_format($size,0,',','.') , " bytes.\n";
    }

  }

  /**
    * Analiza la sintaxis de la parte que crea las tablas en SQL
    *
    * @param int $extra : Son las posiciones a partir de la 2 desde la que se encuentra este apartado
    * en la query desde la consola.
  */
  private function checkSQLSyntax(int $e = 2) {
    try {
      # Existencia de 'nombre_tabla' y '[campos]'
      if(!array_key_exists(2 + $e,$this->args) || !array_key_exists(3 + $e,$this->args)) {
        throw new Exception("\nERROR: La sintaxis para creación de la tabla es incorrecta. \n");
      }

      # Correcta sintaxis en '[campos]'
      if($this->args[3 + $e][0] != '[' || $this->args[3 + $e][strlen($this->args[3 + $e]) - 1] != ']') {
        throw new Exception("\nERROR: La sintaxis para los campos debe empezar con '[' y terminar con ']' sin espacios. \n");
      }

      # Separamos cada campo y nos traemos el primero y ultimo con las llaves
      $campos = explode(',',$this->args[3 + $e]);

      # Sacamos las llaves del primero y del último
      $campos[0][0] = str_replace('[',' ',$campos[0][0]);
      $campos[sizeof($campos) - 1][strlen($campos[sizeof($campos) - 1]) - 1] = str_replace(']',' ',$campos[sizeof($campos) - 1][strlen($campos[sizeof($campos) - 1]) - 1]);

      # Llenamos $this->fields
      foreach ($campos as $c) {
        $exp = explode(':',$c);
        if(!array_key_exists(1,$exp) || empty($exp[1])) {
          throw new Exception("\nERROR: La sintaxis debe ser campo:tipo[longitud]. \n");
        }
        $this->fields[strtolower($exp[0])] = str_replace('[','(',str_replace(']',')',$exp[1]));
      }

      # Guardamos el nombre de la tabla
      $this->table_name = $this->args[2 + $e];

    } catch (Exception $e) {
      echo $e->getMessage() , "\n";
      exit(1);
    }
  }

  /**
    * Genera la consulta SQL basada en la información que existe en $this->table_name y $this->fields
    * Se conecta con la base de datos a la que está conectada el framework y hace una query.
    * El usuario de conexión debe tener permiso de escritura en la base de datos.
  */
  private function generateSQL() {
    # Conectamos con la base de datos del framework
    require_once('core/kernel/Conexion.php');
    $this->db = Conexion::Start(DATABASE['name'],DATABASE['motor'],false);

    # Crear la tabla
    $SQL = 'CREATE TABLE `' . $this->table_name . '` (';
    $sizeof = sizeof($this->fields);
    $i = 1;

    # Llenar la query con los campos
    foreach ($this->fields as $name => $tipo) {
      # `campo` tipo(longitud)
      $SQL .= ' `' . trim($name) . '` ' . $tipo . (trim($name) == 'id' ? ' NOT NULL AUTO_INCREMENT' : ' NOT NULL');

      # Agregar la coma
      if($i < $sizeof) {
        $SQL .= ' , ';
      } $i++;
    }

    # Agregar clave primaria si un campo se llama 'id'
    if(array_key_exists('id',$this->fields) || array_key_exists(' id',$this->fields)) {
      $SQL .= ', PRIMARY KEY (`id`)';
    }

    # Cerrar la query
    $SQL .= " ) ENGINE='InnoDB' DEFAULT CHARSET='utf8' COLLATE='utf8_unicode_ci';";

    # Ejecutar la query
    $this->db->query($SQL);

    # Mensaje de éxito
    echo "Se ha creado la tabla $this->table_name. \n";
  }

  /**
    * Genera los módulos que estén en la query de la consola.
  */
  private function generateModules() {
    # Salto de línea
    echo "\n";

    # Modelo (debe existir si o si) y tabla en base de datos
    if($this->model && $this->database) {
      $this->checkSQLSyntax();
      $this->generateSQL();
    }

    # Crear el modelo SIN api
    if($this->model && -1 == $this->api) {
      $this->generateFile(0,'./generator/m.g');
    # Crear el modelo CON api
    } else if($this->model && -1 < $this->api) {
      $this->generateFile(0,'./generator/ma.g');
      $this->writeApi(); # escribir en el 'method'.php la llamda a este modelo y retorno en json
    }

    # Crear controlador
    if($this->controller) {
      $this->generateFile(1,'./generator/c.g');
    }

    # Crear vista SIN api
    if($this->view && -1 == $this->api ) {
      $this->generateFile(2,'./generator/'.self::TPL_FOLDER.'v.g');
    # Crear vista CON api (debe haber un modelo si o si)
    } else if ($this->model && $this->view && -1 < $this->api) {
      $this->generateFile(2,'./generator/'.self::TPL_FOLDER.'va.g');
      $this->generateFile(3,'./generator/js.g',strtoupper($this->api_http[$this->api]),$this->viewName);
    }

  }

  /**
    * Define los nombres para el Modelo, Vista y Controlador respectivos, también analiza si son válidos.
  */
  private function checkModuleName() {
    try {
      # Si no existe un nombre para el modulo
      if(!array_key_exists(2,$this->args)) {
        throw new Exception("ERROR: Debes colocar el nombre para el modulo.\n");
      }

      # Si el nombre es 'b', quiere crear una tabla y no ha puesto el nombre del modulo
      if($this->args[2] == 'b') {
        throw new Exception("ERROR: Debes colocar el nombre para el modulo antes del atributo 'b'.\n");
      }

      # Si no es alfanumérico no es un nombre válido
      if(!ctype_alnum(trim($this->args[2]))) {
        throw new Exception("ERROR: El nombre del modulo debe ser alfanumerico.\n");
      }

      # Formato del nombre para cada tipo de elemento
      $this->modelName = ucwords($this->args[2]);
      $this->controllerName = strtolower($this->args[2]) . 'Controller';
      $this->viewName = strtolower($this->args[2]);
    } catch (Exception $e) {
      echo $e->getMessage();
      exit(1);
    }
  }

  /**
    * Encargado de interpretar la estructura de los comandos
  */
  private function lexer() {
    try {
      # Si pide ayuda
      if($this->args[1] == '-ayuda') {
        $this->showHelp();

      # Vemos si es un CRUD entero
      } else if($this->args[1] == 'crud') {
        # Revisamos el nombre del módulo
        $this->checkModuleName();

        # Tabla en base de datos (b nombre_tabla [campos])
        if(array_key_exists(3,$this->args) && $this->args[3] == 'b') {
          $this->database = true;
        } else {
          throw new Exception("El CRUD debe crearse con una tabla en la base de datos.");
        }

        # Api REST siempre como POST para los CRUD
        $this->api = 1;

        # Generamos el CRUD
        $this->generateCrud();

      # Vemos si es sólo la creación de una tabla
      } else if($this->args[1] == 'b') {
        # Revisar sintaxis
        $this->checkSQLSyntax(0);
        # Generar consulta y crear la tabla
        $this->generateSQL();

      # Analizamos la sintaxis del comando
      } else {
        # Revisamos el nombre del módulo
        $this->checkModuleName();

        # Control para saber si solo se indicaron los módulos correctos
        $lexer = false;

        # Modelo
        if(strpos($this->args[1], 'm') !== false) {

          $lexer = true;
          $this->model = true;

          # API GET
          if(strpos($this->args[1], 'a:get') !== false) {
            $this->api = 0;
          # API POST
          } else if(strpos($this->args[1], 'a:post') !== false) {
            $this->api = 1;
          # API PUT
          } else if(strpos($this->args[1], 'a:put') !== false) {
            $this->api = 2;
          # API DELETE
          } else if(strpos($this->args[1], 'a:delete') !== false) {
            $this->api = 3;
          # API MAP [GET || POST]
          } else if(strpos($this->args[1], 'a:map') !== false) {
            $this->api = 4;
          }

          # Tabla en base de datos (b nombre_tabla [campos])
          if(array_key_exists(3,$this->args) && $this->args[3] == 'b') {
            $this->database = true;
          }

        }

        # Controlador
        if(strpos($this->args[1], 'c') !== false) {
          $lexer = true;
          $this->controller = true;
        }

        # Vista
        if(strpos($this->args[1], 'v') !== false) {
          $lexer = true;
          $this->view = true;
        }

        # No indicó que quería crear correctamente
        if(!$lexer) {
          throw new Exception("ERROR: Problema en la sintaxis, para informacion usar:\n- php gen.php -ayuda\n");

        # Se crearán los módulos indicados
        } else {
          $this->generateModules();
        }

      }
    } catch (Exception $e) {
      echo $e->getMessage();
      exit(1);
    }
  }

  /**
    * Constructor del generador
    *
    * @param array $a: Todos los argumentos pasados por consola
    * @param int $c: Cantidad de argumentos
    *
  */
  public function __construct(array $a, int $c) {
    $this->args = $a;
    $this->count_args = $c - 1;
    $this->lexer();
  }

}

?>
