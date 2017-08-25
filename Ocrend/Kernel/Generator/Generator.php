<?php

/*
 * This file is part of the Ocrend Framewok 2 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ocrend\Kernel\Generator;

use Ocrend\Kernel\Database\Database;

/**
 * Generador de scripts en PHP
 *
 * @author Brayan Narváez <prinick@ocrend.com>
*/

final class Generator {

    /**
      * Contiene los argumentos pasados por la consola
      *
      * @var array 
    */
    private $arguments = array();

    /**
      * Ruta de modelos en la aplicación
      *
      * @var string
    */
    const R_MODELS = './app/models/';

    /**
      * Ruta de controladores en la aplicación
      *
      * @var string
    */
    const R_CONTROLLERS = './app/controllers/';

    /**
      * Ruta de templates en la aplicación
      *
      * @var string
    */
    const R_TEMPLATES = './app/templates/';

    /**
      * Ruta de assets en la aplicación
      *
      * @var string
    */
    const R_VIEWS = './views/app/';

    /**
      * Ruta de la api rest en la aplicación
      *
      * @var string
    */
    const R_API = './api/http/';

    /**
      * Verbos HTTP compatibles
      *
      * @var string
    */
    const API_HTTP_VERBS = ['get','post','put','delete'];

    /**
      * Ruta de plantillas para leer
      *
      * @var string
    */
    const TEMPLATE_DIR = './Ocrend/Kernel/Generator/Templates/';

    /**
      * Nombre del módulo a escribir.
      *
      * @var array
    */
    private $name = array(
      'controller' => null, #nombreController
      'model' => null, #Nombre
      'view' => null #nombre
    );

    /**
      * Módulos a escribir
      *
      * @var array
    */
    private $modules = array(
      'model' => false,
      'view' => false,
      'controller' => false,
      'ajax' => false,
      'database' => false,
      'crud' => false,
      'api' => null # contiene el verbo http
    ); 

    /**
      * Nombre de la tabla a crear en la base de datos.
      *
      * @var string
    */
    private $table_name = '';

    /**
      * Colección de tablas para la base de datos.
      * Con la forma:
      * {'nombre_tabla' => array('tipo' => string , 'longitud' => null|int )}
      *
      * @var array
    */
    private $tablesCollection = array();

    /**
      * Crea el contenido de una tabla.
      *
      * @return string con el contenido del formulario
    */
    private function createViewTableContent() : string {
      # Obtener el formato de la tabla
      $table = $this->readFile(self::TEMPLATE_DIR . 'Twig/table.twig');

      # Obtener el formato del thead
      $thead = $this->readFile(self::TEMPLATE_DIR . 'Twig/Resources/thead.twig');

      # Obtener el formato del tbody
      $tbody = $this->readFile(self::TEMPLATE_DIR . 'Twig/Resources/tbody.twig');

      # Obtener el formato de los actions
      $actions = $this->readFile(self::TEMPLATE_DIR . 'Twig/Resources/actions.twig');
      $actions = str_replace('{{view}}',$this->name['view'],$actions);
      $actions = str_replace('{{id_element}}','{{ d.id_'.$this->table_name.' }}',$actions);

      # Obtener el formato del títutlo de las acciones
      $actions_title = $this->readFile(self::TEMPLATE_DIR . 'Twig/Resources/actions_title.twig');

      # Reemplazos base
      $table = str_replace('{{view}}',$this->name['view'],$table);
      $table = str_replace('{{model}}',$this->name['model'],$table);

      # Inicio del thead
      $thead_final = "<tr>\n";
      # Inicio del tbody
      $tbody_final = "{% for d in data if false != data %}\n<tr>\n";

      # Recorrer los campos de la tabla creada
      foreach($this->tablesCollection as $name => $field_info) {
        # Hacer thead
        $thead_final .= "\t" . str_replace('{{name}}',ucwords(str_replace('_',' ',$name)),$thead) . "\n";
        # Hacer tbody
        $tbody_final .= str_replace('{{name}}','{{ d.'.$name.' }}',$tbody) . "\n";
      }

      # TR final del unico elemento en el thead con el título de las acciones
      $thead_final .= "$actions_title \n</tr>";

      # Añadir las acciones 
      $tbody_final .= "$actions \n";
      # TR final de cada elemento en el tbody
      $tbody_final .= "</tr>\n{% endfor %}";

      # Reemplazo final
      $table = str_replace('{{thead}}',$thead_final,$table);
      $table = str_replace('{{tbody}}',$tbody_final,$table);

      return $table;
    }

    /**
      * Crea el contenido de una vista de formulario.
      *
      * @param bool $is_crud: Utilizado para saber si se llama desde un crud
      * @param bool $edit: En caso de que sea desde un crud, se usa para definir los {{value}} automáticos
      * 
      * @return string con el contenido del formulario
    */
    private function createViewFormContent(bool $is_crud, bool $edit = false) : string {
      # Obtener formato para inputs
      $inputs = $this->readFile(self::TEMPLATE_DIR . 'Twig/Resources/inputs.twig');

      # Obtener la vista
      $form = $this->readFile(self::TEMPLATE_DIR . 'Twig/form.twig');

      # Reemplazar datos básicos
      $form = str_replace('{{view}}',$this->name['view'],$form);
      $form = str_replace('{{model}}',$this->name['model'],$form);

      # Verificar si hay campos para una base de datos
      if($this->modules['database']) {
        # Desde la edición
        if($is_crud) {
          if($edit) {
            $form = str_replace('{{ajax_file_name}}','editar',$form);
          } else {
            $form = str_replace('{{ajax_file_name}}','crear',$form);
          }
        } else {
          $form = str_replace('{{ajax_file_name}}',$this->name['view'],$form);
        }
        
        # Conjunto de inputs
        $final_inputs = "\n";
        # Reemplazar inputs
        foreach($this->tablesCollection as $name => $field_info) {
          # Si puede ser de tipo email
          if(false !== strpos($name,'email')) {
             $field_input = str_replace('{{type_input}}','email',$inputs);
          # Si puede ser un teléfono 
          } elseif(false !== strpos($name,'phone') 
               || false !== strpos($name,'telefono') 
               || false !== strpos($name,'celular')) {
             $field_input = str_replace('{{type_input}}','tel',$inputs);
          # Si no, un texto cualquiera
          } else {
            $field_input = str_replace('{{type_input}}','text',$inputs);
          }
          
          # Desde la edición
          if($edit) {
            $field_input = str_replace('{{value}}','{{ data.'.$name.' }}',$field_input);
          # Desde la creación o sin crud
          } else {
            $field_input = str_replace('{{value}}','',$field_input);
          }
          
          # Últimos retoques
          $field_input = str_replace('{{name}}',$name,$field_input);
          $field_input = str_replace('{{label}}',ucwords(str_replace('_',' ',$name)),$field_input);

          # Añadir el input al formulario
          $final_inputs .= "$field_input\n\n";
        }
        
        # Reemplazo final
        $form = str_replace('{{inputs}}',$final_inputs,$form);    

        # Campo oculto
        if($edit) {
          $form = str_replace('{{hiddens}}',"<input type=\"hidden\" name=\"id_" . $this->table_name . "\" value=\"{{ data.id_" . $this->table_name . " }}\" />\n",$form);
        } else {
          $form = str_replace('{{hiddens}}','',$form);
        }
        
      }
      # Si no, es un formulario por defecto que tiene ajax 
      else {
        # Reemplazar datos básicos
        $form = str_replace('{{ajax_file_name}}',$this->name['view'],$form);
        # Reemplazar inputs
        $inputs = str_replace('{{type_input}}','text',$inputs);
        $inputs = str_replace('{{value}}','',$inputs);
        $inputs = str_replace('{{name}}','ejemplo',$inputs);
        $inputs = str_replace('{{label}}','Campo De Ejemplo',$inputs);
        # Reemplazo final
        $form = str_replace('{{hiddens}}','',$form);
        # Campo oculto
        $form = str_replace('{{inputs}}',$inputs,$form);        
      }

      return $form;
    }

    /**
      * Crea las vistas solicitadas por comando
      *
      * @return void
    */
    private function createViews() {
      # Ruta para las vistas
      $ruta = self::R_TEMPLATES . $this->name['view'] . '/';

      # Crear ruta si no existe
      if(!is_dir($ruta)) {
        mkdir($ruta,0777,true);
      # Si existe la ruta existen ficheros dentro
      } else {
        throw new \Exception('La ruta ' . $ruta . ' ya existe.');
      }

      # Si es un crud se utilizan form.twig y table.twig con toda la carpeta Templates/Twig/Resources
      if($this->modules['crud']) {
        # Obtener la vista CREACIÓN
        $form = $this->createViewFormContent(true);
        # Crear la vista CREACIÓN
        $this->writeFile($ruta . 'crear.twig',$form);
        $this->writeLn('Se ha creado el fichero ' . $ruta . 'crear.twig');

        # Obtener la vista EDICIÓN
        $form = $this->createViewFormContent(true,true);
        # Crear la vista EDICIÓN
        $this->writeFile($ruta . 'editar.twig',$form);
        $this->writeLn('Se ha creado el fichero ' . $ruta . 'editar.twig');

        # Obtener la vista LISTADO
        $table = $this->createViewTableContent();
        # Crear la vista LISTADO
        $this->writeFile($ruta . $this->name['view'] .'.twig',$table);
        $this->writeLn('Se ha creado el fichero ' . $ruta . $this->name['view'] .'.twig');
      }
      # Si hay un modelo y una petición ajax, vista form.twig
      else if($this->modules['model'] && ($this->modules['ajax'] || null !== $this->modules['api'])) {
        # Obtener la vista
        $form = $this->createViewFormContent(false);
        # Crear la vista
        $this->writeFile($ruta . $this->name['view'] . '.twig',$form);
        $this->writeLn('Se ha creado el fichero ' . $ruta . $this->name['view'] . '.twig');
      } 
      # Vista por defecto cuando no hay modelos ni crud blank.twig
      else {
        # Obtener la vista
        $blank = $this->readFile(self::TEMPLATE_DIR . 'Twig/blank.twig');
        # Crear la vista
        $this->writeFile($ruta . $this->name['view'] . '.twig',$blank);
        $this->writeLn('Se ha creado el fichero ' . $ruta . $this->name['view'] . '.twig');
      }
    }

    /**
      * Escribe un mensaje en consola y salta de línea 
      *
      * @param null|string $msg: Mensaje 
      *
      * @return void
    */
    private function writeLn($msg = null) {
        if(null != $msg) {
            echo "$msg";
        } 
        
        echo "\n";
    }

    /**
      * Devuelve un string con el contenido de un archivo
      *
      * @param string $dir: Directorio del archivo a leer
      *
      * @return string : con contenido del archivo
    */
    private function readFile(string $dir) : string {
      $lines = '';
      $f = new \SplFileObject($dir);
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
      * @throws \Exception si el fichero ya existe
      * @return int :catidad de bytes escritos en el archivo
    */
    private function writeFile(string $dir, string $content) : int {
      if(file_exists($dir)) {
        throw new \Exception('El fichero ' . $dir . ' ya existe.');
      }
      
      $f = new \SplFileObject($dir,'w');
      return (int) $f->fwrite($content);
    }
    /**
      * Escribe un string al final, en un archivo existente
      *
      * @param string $dir: Directorio del archivo sobre el cual se va a escribir
      * @param string $content: Contenido a escribir
      *
      * @return int : catidad de bytes escritos en el archivo
    */
    private function writeInFile(string $dir, string $content) : int {
      $f = new \SplFileObject($dir,'a+');
      return (int) $f->fwrite("\n\n" . $content);
    }

    /**
      * Crea la tabla en la base de datos.
      *
      * @return void
    */
    private function createTable() {
      # Verificar si hay que crear una tabla en la base de datos
      $sizeof = sizeof($this->tablesCollection);
      if($sizeof > 0) {
        global $config;
        
        # Abrir conexión a la base de datos
        $db = Database::Start(
          $config['database']['name'],
          $config['database']['motor'],
          false
        );
        
        # Empezar a escribir
        $SQL = 'CREATE TABLE `' . $this->table_name . '` (';
        
        # Crear el campo del id
        $SQL .= ' `id_' . $this->table_name . '` INT(11) NOT NULL AUTO_INCREMENT,'; 
        
        # Llenar la query con los campos
        $i = 1;
        foreach ($this->tablesCollection as $name => $field_info) {
          # `campo` tipo(longitud)
          $SQL .= ' `' . trim($name) . '` ' . $field_info['tipo'] . (null !== $field_info['longitud'] ? '('.trim($field_info['longitud']).') ' : ' ') .  'NOT NULL';
        
          # Agregar la coma
          if($i < $sizeof) {
            $SQL .= ' , ';
          } $i++;
        }
        
        # Cerrar la query
        $SQL .= ', PRIMARY KEY (`id_' . $this->table_name . '`)';
        $SQL .= " ) ENGINE='InnoDB' DEFAULT CHARSET='utf8' COLLATE='utf8_unicode_ci';";
              
        # Crear la query
        $db->query($SQL);
        
        # Cerrar la conexión
        $db = null;
        
        # Mostrar mensaje por pantalla
        $this->writeLn('Se ha creado la tabla ' . $this->table_name . ' en la base de datos ' . $config['database']['name']);
      }
    }

    /**
      * Se encarga de definir el contenido que tendrá un controlador de acuerdo al comando.
      * El contenido va a variar si se está haciendo un CRUD, si se creó también un modelo o una vista.
      *
      * @return string : {{content}} del controlador
    */
    private function createControllerContent() : string {
      $content = "// Contenido del controlador... \n";
      # Si es un controlador de crud
      if($this->modules['crud']) {
        $content = "global \$config;
        
        \${{model_var}} = new Model\\{{model}}(\$router);

        switch(\$this->method) {
          case 'crear':
            echo \$this->template->render('{{view}}/crear');
          break;
          case 'editar':
            if(\$this->isset_id and false !== (\$data = \${{model_var}}->get(false))) {
              echo \$this->template->render('{{view}}/editar', array(
                'data' => \$data[0]
              ));
            } else {
              \$this->functions->redir(\$config['site']['url'] . '{{view}}/&error=true');
            }
          break;
          case 'eliminar':
            \${{model_var}}->delete();
          break;
          default:
            echo \$this->template->render('{{view}}/{{view}}',array(
              'data' => \${{model_var}}->get()
            ));
          break;
        }";
      } else {
        # Si existe un modelo
        if($this->modules['model']) {
          $content = "\${{model_var}} = new Model\\{{model}};\n";
        }
        # Si existe una vista
        if($this->modules['view']) {
          $content .= "\t\techo \$this->template->render('{{view}}/{{view}}');\n";
        }
      }

      return $content;
    }

    /**
      * Se encarga de definir el contenido que tendrá un modelo de acuerdo al comando.
      * El contenido va a variar si se está haciendo un CRUD, si se ha definido un fichero ajax,
      * si se ha escrito en la api y si se ha creado una tabla en la base de datos.
      *
      * @return string : {{content}} del modelo
    */
    private function createModelContent() : string {
      $content = "// Contenido del modelo... \n";
      # Si es el modelo de un crud
      if($this->modules['crud']) {
        
        # Campos de la base de datos
        $database_fields = '';
        $size = sizeof($this->tablesCollection);
        $i = 1;
        foreach($this->tablesCollection as $field => $data) {
          $database_fields .= "\t\t\t\t\t'$field' => \$http->request->get('$field')";
          if($i < $size) {
            $database_fields .= ",\n";
          } 
          $i++;
        }

        # Contenido
        $content = "
    /**
      * Controla los errores de entrada del formulario
      *
      * @throws ModelsException
    */
    final private function errors() {
      global \$http;
      # throw new ModelsException('¡Esto es un error!');
    }

    /** 
      * Crea un elemento de {{model}} en la tabla `{{table_name}}`
      *
      * @return array con información para la api, un valor success y un mensaje.
    */
    final public function add() {
      try {
        global \$http;
                  
        # Controlar errores de entrada en el formulario
        \$this->errors();

        # Insertar elementos
        \$this->db->insert('{{table_name}}',array(
$database_fields
        ));

        return array('success' => 1, 'message' => 'Creado con éxito.');
      } catch(ModelsException \$e) {
        return array('success' => 0, 'message' => \$e->getMessage());
      }
    }
          
    /** 
      * Edita un elemento de {{model}} en la tabla `{{table_name}}`
      *
      * @return array con información para la api, un valor success y un mensaje.
    */
    final public function edit() : array {
      try {
        global \$http;

        # Obtener el id del elemento que se está editando y asignarlo en \$this->id
        \$this->setId(\$http->request->get('{{id_table_name}}'),'No se puede editar el elemento.'); 
                  
        # Controlar errores de entrada en el formulario
        \$this->errors();

        # Actualizar elementos
        \$this->db->update('{{table_name}}',array(
$database_fields
        ),\"{{id_table_name}}='\$this->id'\",'LIMIT 1');

        return array('success' => 1, 'message' => 'Editado con éxito.');
      } catch(ModelsException \$e) {
        return array('success' => 0, 'message' => \$e->getMessage());
      }
    }

    /** 
      * Borra un elemento de {{model}} en la tabla `{{table_name}}`
      * y luego redirecciona a {{view}}/&success=true
      *
      * @return void
    */
    final public function delete() {
      global \$config;
      # Borrar el elemento de la base de datos
      \$this->db->delete('{{table_name}}',\"{{id_table_name}}='\$this->id'\");
      # Redireccionar a la página principal del controlador
      \$this->functions->redir(\$config['site']['url'] . '{{view}}/&success=true');
    }

    /**
      * Obtiene elementos de {{model}} en la tabla `{{table_name}}`
      *
      * @param bool \$multi: true si se quiere obtener un listado total de los elementos 
      *                     false si se quiere obtener un único elemento según su {{id_table_name}}
      * @param string \$select: Elementos de {{table_name}} a seleccionar
      *
      * @return false|array: false si no hay datos.
      *                      array con los datos.
    */
    final public function get(bool \$multi = true, string \$select = '*') {
      if(\$multi) {
        return \$this->db->select(\$select,'{{table_name}}');
      }

      return \$this->db->select(\$select,'{{table_name}}',\"{{id_table_name}}='\$this->id'\",'LIMIT 1');
    }\n";
      } else {
        # Si existe una escritura en la api
        if(null !== $this->modules['api'] || $this->modules['ajax']) {
          $content = "
    /**
      * Devuelve un arreglo para la api
      *
      * @return array
    */
    final public function foo() : array {
      global \$http;

      return array('success' => 1, 'message' => 'Funcionando');
    }\n";
        }
        # Si hay una tabla nueva creada
        if($this->modules['database']) {
          $content .= "\n\n\t\t/**
      * Obtiene elementos de {{model}} en {{table_name}}
      *
      * @param string \$select: Elementos de {{table_name}} a seleccionar
      *
      * @return false|array: false si no hay datos.
      *                     array con los datos.
    */
    final public function get(string \$select = '*') {
      return \$this->db->select(\$select,'{{table_name}}');
    }\n";
        }
      }
      
      return $content;
    }

    /**
      * Crea el contenido que se escribirá para la petición rest en uno de los verbos HTTP.
      * El contenido dependerá de si se está creando o no un modelo también.
      *
      * @return void 
    */
    private function createApiContent(bool $model) : string {
      if($model) {
        return "\n/**
  * Acción vía ajax de {{model}} en api/{{view}}
  *
  * @return json
*/\n\$app->{{method}}('/{{view}}', function() use(\$app) {
  \${{model_var}} = new Model\{{model}}; 

  return \$app->json(\${{model_var}}->{{method_model}}());   
});";
      }

      return "\n/**
  * Acción vía api/{{view}}
  *
  * @return json
*/\n\$app->{{method}}('/{{view}}', function() use(\$app) {
  return \$app->json(array('success' => 0, 'message' => 'Funcionando.'));   
});";

    }

    /**
      * Crea un controlador según la configuración de los comandos.
      *
      * @return void 
    */
     private function createController() {
      global $config;

      # Obtener contenido
      $content = $this->createControllerContent();
      
      # Cargar plantilla
      $route = self::R_CONTROLLERS . $this->name['controller'] .'.php';
      $content = str_replace('{{content}}',$content,$this->readFile(self::TEMPLATE_DIR . 'controller.php'));

      # Créditos
      $content = str_replace('{{author}}',$config['site']['author'],$content);
      $content = str_replace('{{author_email}}',$config['site']['author_email'],$content);
    
      # Información
      $content = str_replace('{{model_var}}',strtolower($this->name['model'][0]),$content);
      $content = str_replace('{{model}}',$this->name['model'],$content);
      $content = str_replace('{{view}}',$this->name['view'],$content);
      $content = str_replace('{{controller}}',$this->name['controller'],$content);

      # Crear el archivo
      $this->writeFile($route,$content);
      $this->writeLn('Creado el controlador ' . $route);
    }

    /**
      * Crea un modelo según la configuración de los comandos.
      *
      * @return void 
    */
    private function createModel() {
      global $config;

      # Obtener contenido
      $content = $this->createModelContent();

      # Base de datos
      if($this->modules['database']) {
        $content = str_replace('{{table_name}}',$this->table_name,$content);
        $content = str_replace('{{id_table_name}}','id_' . $this->table_name,$content);
      }
      
      # Cargar Plantilla
      $route = self::R_MODELS . $this->name['model'] .'.php';
      $content = str_replace('{{content}}',$content,$this->readFile(self::TEMPLATE_DIR . 'model.php'));

      # Base de datos
      if($this->modules['database']) {
        $content = str_replace('{{trait_db_model}}','/**
      * Característica para establecer conexión con base de datos. 
    */
    use DBModel;',$content);
        $content = str_replace('{{trait_db_model_construct}}','$this->startDBConexion();',$content);
        $content = str_replace('{{trait_db_model_destruct}}','$this->endDBConexion();',$content);
      }
      # Si no hay 
      else {
        $content = str_replace('{{trait_db_model}}','',$content);
        $content = str_replace('{{trait_db_model_construct}}','',$content);
        $content = str_replace('{{trait_db_model_destruct}}','',$content);
      }

      # Créditos
      $content = str_replace('{{author}}',$config['site']['author'],$content);
      $content = str_replace('{{author_email}}',$config['site']['author_email'],$content);
    
      # Información
      $content = str_replace('{{model}}',$this->name['model'],$content);
      $content = str_replace('{{view}}',$this->name['view'],$content);

      # Crear el archivo
      $this->writeFile($route,$content);
      $this->writeLn('Creado el modelo ' . $route);
    }
    
    /**
      * Crea los archivos javascript necesarios para el funcionamiento con ajax.
      *
      * @return void 
    */
    private function createAjax() {
      # Obtener fuente
      $content = $this->readFile(self::TEMPLATE_DIR . 'ajax.js');

      # Reemplazar comunes
      $content = str_replace('{{view}}',$this->name['view'],$content);

      # Si es un crud son dos javascript
      if($this->modules['crud']) {
        # Apuntar a la api
        $content = str_replace('{{method}}','POST',$content);
        $add = str_replace('{{rest}}',$this->name['view'] . '/crear',$content);
        $edit = str_replace('{{rest}}',$this->name['view'] . '/editar',$content);
        
        # Rutas
        $route_add = self::R_VIEWS . 'js/' . $this->name['view'] . '/crear.js';
        $route_edit = self::R_VIEWS . 'js/' . $this->name['view'] . '/editar.js';

        # Crear la carpeta
        mkdir(self::R_VIEWS . 'js/' . $this->name['view'] .'/',0777,true);

        # Crear los archivos
        $this->writeFile($route_add,$add);
        $this->writeFile($route_edit,$edit);

        # Mostrar en consola
        $this->writeLn('Se ha creado ' . $route_add);
        $this->writeLn('Se ha creado ' . $route_edit);
      }
      # Si no es un crud, es uno solo
      else {
        $content = str_replace('{{method}}',strtoupper($this->modules['api']),$content);

        # Apuntar a la api
        $content = str_replace('{{rest}}',$this->name['view'],$content);

        # Ruta
        $route = self::R_VIEWS . 'js/' . $this->name['view'] . '/' . $this->name['view'] . '.js';

        # Crear la carpeta
        mkdir(self::R_VIEWS . 'js/' . $this->name['view'] .'/',0777,true);
        
        # Crear archivo
        $this->writeFile($route,$content);

        # Mostrar en consola
        $this->writeLn('Se ha creado ' . $route);
      }
    }

    /**
      * Escribe en el fichero del verbo HTTP correspondiente en la api.
      *
      * @return void 
    */
    private function writeInAPI() {
      # Si es un crud son dos peticiones rest 
      if($this->modules['crud']) {
        # Comunes para edit & add
        $add = $this->createApiContent(true);
        $add = str_replace('{{method}}','post',$add);
        $add = str_replace('{{model_var}}',strtolower($this->name['model'][0]),$add);
        $add = str_replace('{{model}}',$this->name['model'],$add);

        $edit = $add;

        # Propios de add
        $add = str_replace('{{view}}',$this->name['view'] . '/crear',$add);
        $add = str_replace('{{method_model}}','add',$add);

        # Propios de edit
        $edit = str_replace('{{view}}',$this->name['view'] . '/editar',$edit);
        $edit = str_replace('{{method_model}}','edit',$edit);

        # Escribir en add y edit
        $route = self::R_API . 'post.php';
        $this->writeInFile($route,$add);
        $this->writeInFile($route,$edit);
      }
      # Si no es un crud, se siguen las reglas 
      else {
        # Comunes
        $content = $this->createApiContent($this->modules['model']); 
        $content = str_replace('{{method}}',$this->modules['api'],$content);
        $content = str_replace('{{view}}',$this->name['view'],$content);

        # Si existe un modelo
        if($this->modules['model']) {
          $content = str_replace('{{model_var}}',strtolower($this->name['model'][0]),$content);
          $content = str_replace('{{model}}',$this->name['model'],$content);
          $content = str_replace('{{method_model}}','foo',$content);
        }
        
        # Escribir en el archivo
        $route = self::R_API . $this->modules['api'] . '.php';
        $this->writeInFile($route,$content);
      }

      # Mostrar mensaje
      $this->writeLn('Se ha escrito en ' . $route);

      # Crear fichero javascript
      if($this->modules['crud'] || $this->modules['ajax'] || null !== $this->modules['api']) {
        $this->createAjax();
      }
    }

    /**
      * Lanzador para empezar a crear todos los archivos según los comandos.
      *
      * @return void 
    */
    private function buildFiles() {
      # Crear tabla en la base de datos
      if($this->modules['crud'] || $this->modules['database']) {
        $this->createTable();
      }

      # Crear controlador
      if($this->modules['crud'] || $this->modules['controller']) {
        $this->createController();
      }

      # Crear modelo
      if($this->modules['crud'] || $this->modules['model']) {
        $this->createModel();
      }

      # Escribir en la api rest 
      if($this->modules['ajax'] || $this->modules['api'] !== null || $this->modules['crud']) {
        $this->writeInAPI();
      }

      # Crear vista
      if($this->modules['crud'] || $this->modules['view']) {
        $this->createViews();
      }
    }

    /**
      * Muestra información de ayuda a través de la consola
      *
      * @return void
    */
    private function help() {
        $this->writeLn();
        $this->writeLn('Comandos disponibles ');
        $this->writeLn('-------------------------------------');
        $this->writeLn();
        $this->writeLn('Crear un crud conectado a la base de datos:');
        $this->writeLn('app:crud [Nombre] [Nombre de la tabla en la DB] campo1:tipo:longitud(opcional) campo2:tipo ...');
        $this->writeLn();
        $this->writeLn('Crear un modelo, una vista y un controlador:');
        $this->writeLn('app:mvc [Nombre]');
        $this->writeLn();
        $this->writeLn('Crear una vista y un controlador:');
        $this->writeLn('app:vc [Nombre]');
        $this->writeLn();
        $this->writeLn('Crear un modelo y un controlador:');
        $this->writeLn('app:mc [Nombre]');
        $this->writeLn();
        $this->writeLn('Crear un modelo y una vista:');
        $this->writeLn('app:mv [Nombre]');
        $this->writeLn();
        $this->writeLn('Crear un modelo vacio:');
        $this->writeLn('app:m [Nombre]');
        $this->writeLn();
        $this->writeLn('Crear un controlador vacio:');
        $this->writeLn('app:c [Nombre]');
        $this->writeLn();
        $this->writeLn('Crear una vista vacia:');
        $this->writeLn('app:v [Nombre]');
        $this->writeLn();
        $this->writeLn();
        $this->writeLn('Opciones extras, se aniaden al final de una instruccion.');
        $this->writeLn('-------------------------------------');
        $this->writeLn();
        $this->writeLn('Generar un fichero javascript de ajax que apunta a la api rest via POST.');
        $this->writeLn('-ajax');
        $this->writeLn();
        $this->writeLn('Escribe en el fichero del verbo http correspondiente en la api rest.');
        $this->writeLn('-api:[verbo]');
        $this->writeLn();
        $this->writeLn('Agregar caracteristica DBModel a un modelo creado, (No puede haber ninguna otra opcion despues de esta)');
        $this->writeLn('-db');
        $this->writeLn();
        $this->writeLn('Crear una tabla en la base de datos, (No puede haber ninguna otra opcion despues de esta).');
        $this->writeLn('-db [Nombre de la tabla en la DB] campo1:tipo:longitud(opcional) campo2:tipo');
    }

    /**
      * Se encarga de analizar la sintaxis de los comandos y decidir qué hacer
      *
      * @throws \Exception cuando existe algún problema con la sintaxis
      * @return void
    */
    private function lexer() {
      # Cargar la ayuda
      if($this->arguments[0] == '-ayuda' || 
         $this->arguments[0] == '-ashuda' || 
         $this->arguments[0] == '-help') {
        
        $this->help();

        return;
      }

      # Verificar comando
      $action = explode(':',$this->arguments[0]);
      if(sizeof($action) != 2 || $action[0] != 'app') {
        throw new \Exception('El comando inicial debe tener la forma app:accion.');
      }
  
      # Verificar que exista un nombre 
      if(!array_key_exists(1,$this->arguments)) {
        throw new \Exception('Se debe asignar un nombre.');
      } else {

        # Formato del nombre
        if(!preg_match('/^[a-zA-Z]*$/',$this->arguments[1])) {
          throw new \Exception('El nombre para el modulo solo puede contener letras.');
        }

        # Nombres para las partes del módulo
        $this->name['controller'] = strtolower($this->arguments[1]) . 'Controller';
        $this->name['model'] = ucfirst(strtolower($this->arguments[1]));
        $this->name['view'] = strtolower($this->arguments[1]);
      }

      # Saber si se pasaron opciones correctas
      $lexer = false;

      # Revisar lo que debe hacerse 
      if($action[1] == 'crud') {
        $lexer = true;
        $this->modules['crud'] = true;

        # Verificar que exista la opción de base de datos
        if(!array_key_exists(2,$this->arguments) || $this->arguments[2] != '-db') {
          throw new \Exception('El crud necesita el parametro -db.');
        }

      } else {
        # Modelo
        if(strpos($action[1], 'm') !== false) {
          $lexer = true;
          $this->modules['model'] = true;
        }

        # Controlador
        if(strpos($action[1], 'c') !== false) {
          $lexer = true;
          $this->modules['controller'] = true;
        }

        # Vista
        if(strpos($action[1], 'v') !== false) {
          $lexer = true;
          $this->modules['view'] = true;
        }
      }

      # Error
      if(!$lexer) {
        throw new \Exception('Problema en la sintaxis, para informacion usar: php gen.php -ayuda');
      }

      # Existencia de opciones
      if(array_key_exists(2,$this->arguments)) {
        $size = sizeof($this->arguments);
        for($i = 2; $i < $size; $i++) {

          # Base de datos
          if($this->arguments[$i] == '-db') {  
            # Revisar si existe un nombre
            if(array_key_exists($i + 1, $this->arguments)) {
              # Revisar la sintaxis del nombre 
              if(!preg_match('/^[a-zA-Z0-9_]*$/',$this->arguments[$i + 1])) {
                throw new \Exception('El formato del nombre debe ser alfanumerico y el unico caracter extra permitido es el " _ "');
              }
              #  Establecer el nombre
              $this->table_name = strtolower($this->arguments[$i + 1]);

               # Revisar que existe al menos un campo
              if(!array_key_exists($i + 2, $this->arguments)) {
                throw new \Exception('Se necesita al menos un campo para la tabla.');
              }

              # Recorrer los campos y revisar la sintaxis uno a uno
              for($x = $i + 2; $x < $size; $x++) {
                $campo = explode(':',$this->arguments[$x]);
                # Requisito mínimo, nombre y tipo
                if(sizeof($campo) >= 2) {
                  # Formato del nombre
                  if(!preg_match('/^[a-zA-Z0-9_]*$/',$campo[0])) {
                    throw new \Exception('El formato del nombre del campo '. $campo[0] .' debe ser alfanumerico y el unico caracter extra permitido es el " _ "');
                  }
                  # Tipo de dato
                  if(!in_array(strtolower($campo[1]),['tinyint','bit','bool','smallint','mediumint','int','bigint','integer','float','xreal','double','decimal','date','datetime','timestamp','char','varchar','tinytext','text','mediumtext','longtext','enum'])) {
                    throw new \Exception('El tipo de dato ' . $campo[1] . ' no existe.');
                  }
                  # Almacenar en la colección
                  $this->tablesCollection[$campo[0]] = array(
                    'tipo' => strtoupper($campo[1]), 
                    'longitud' => null
                  );
                } else {
                  throw new \Exception('El formato del campo ' . $this->arguments[$x] . ' debe ser nombre:tipo.');
                }

                # Existe longitud
                if(sizeof($campo) == 3) {
                  # Revisar valor de longitud
                  if($campo[2] < 0) {
                    throw new \Exception('La longitud del campo ' . $campo[0] . ' debe ser positiva.');
                  }
                  # Poner longitud
                  $this->tablesCollection[$campo[0]]['longitud'] = $campo[2];
                }
              }
            }

            $this->modules['database'] = true;
            break;
          }

          # Javascript ajax valores por defecto, también los mismos si existe un crud
          if($this->arguments[$i] == '-ajax' || $this->modules['crud']) {
            $this->modules['ajax'] = true;
            $this->modules['api'] = 'post';
          }

          # Api rest 
          if(strpos($this->arguments[$i], '-api:') !== false) {
            if(in_array(strtolower($this->arguments[$i]),['-api:get','-api:post','-api:put','-api:delete'])) {
              $this->modules['api'] = strtolower(explode(':',$this->arguments[$i])[1]);
            } else {
              throw new \Exception('El verbo HTTP de la api rest no existe.');
            }
          }
          
        }
      }
    }

    /**
      * Constructor, arranca el generador
      *
      * @throws \Exception si la cantida de argumentos es insuficiente
      * @return void
    */
    public function __construct(array $args) {
      try {
        # Cantidad mínima de argumentos 
        if(sizeof($args) < 2) {
          throw new \Exception('El generador debe tener la forma php gen.php [comandos]');
        }
        # Picar argumentos
        $this->arguments = array_slice($args,1);
        # Espacio en blanco
        $this->writeLn();
        # Empezar a leer los argumentos
        $this->lexer();
        # Comenzar a construir los archivos
        $this->buildFiles();
      } catch(\Exception $e) {
        exit("\nEXCEPCION: " . $e->getMessage() . "\n");
      }
    }

}
