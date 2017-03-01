<?php

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

final class Conexion extends PDO {

  //------------------------------------------------

  private static $inst;

  //------------------------------------------------

  /**
    * Inicia la instancia de conexión, si esta ya ha sido declarada antes, no la duplica y ahorra memoria.
    *
    * @param string $DATABASE, se pasa de forma opcional una base de datos distinta a la definida en DATABASE['name'] para conectar
    *
    * @return la instancia de conexión
  */
  final public static function Start(string $DATABASE = DATABASE['name'], string $MOTOR = DATABASE['motor'], bool $new_instance = false) : Conexion {

    if(!self::$inst instanceof self or $new_instance) {
      self::$inst = new self($DATABASE,$MOTOR);
    }

    return self::$inst;
  }

  //------------------------------------------------

  /**
    * Inicia la conexión con una base de datos
    *
    * @param string $DATABASE, se pasa de forma opcional una base de datos distinta a la definida en DATABASE['name'] para conectar
    *
    * @return void
  */
  final public function __construct(string $DATABASE = DATABASE['name'], string $MOTOR = DATABASE['motor']) {
    try {

      switch ($MOTOR) {
        case 'sqlite':
          parent::__construct('sqlite:'.$DATABASE);
        break;
        case 'cubrid':
          parent::__construct('cubrid:host='.DATABASE['host'].';dbname='.$DATABASE.';port='.DATABASE['port'],DATABASE['user'],DATABASE['pass'],array(
          PDO::ATTR_EMULATE_PREPARES => false,
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        break;
        case 'firebird':
          parent::__construct('firebird:dbname='.DATABASE['host'].':'.$DATABASE,DATABASE['user'],DATABASE['pass'],array(
          PDO::ATTR_EMULATE_PREPARES => false,
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        break;
        case 'odbc';
          parent::__construct('odbc:'.$DATABASE,DATABASE['user'],DATABASE['pass']);
        break;
        case 'oracle':
          parent::__construct('oci:dbname=(DESCRIPTION =
            (ADDRESS_LIST =
              (ADDRESS = (PROTOCOL = '.DATABASE['protocol'].')(HOST = '.$MOTOR.')(PORT = '.DATABASE['port'].'))
            )
            (CONNECT_DATA =
              (SERVICE_NAME = '.$DATABASE.')
            )
          );charset=utf8',DATABASE['user'],DATABASE['pass'],
          array(PDO::ATTR_EMULATE_PREPARES => false,
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC));
        break;
        case 'postgresql':
          parent::__construct('pgsql:host='.DATABASE['host'].';dbname='.$DATABASE.';charset=utf8',DATABASE['user'],DATABASE['pass'],array(
          PDO::ATTR_EMULATE_PREPARES => false,
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        break;
        case 'mysql':
          parent::__construct('mysql:host='.DATABASE['host'].';dbname='.$DATABASE,DATABASE['user'],DATABASE['pass'],array(
          PDO::ATTR_EMULATE_PREPARES => false,
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
        break;
        case 'mssql':
          # Añadido por marc2684
          # DOC: https://github.com/prinick96/Ocrend-Framework/issues/7
          parent::__construct('sqlsrv:Server='.DATABASE['host'].';Database='.$DATABASE.';ConnectionPooling=0',
          DATABASE['user'],
          DATABASE['pass'],
          array( PDO::ATTR_EMULATE_PREPARES => false,
                PDO::SQLSRV_ENCODING_UTF8,
                PDO::ATTR_ERRMODE =>
                PDO::ERRMODE_EXCEPTION
          ));
        break;
        default:
          if(IS_API) {
            die(json_encode(array('success' => 0, 'message' => 'Motor de conexión no identificado.')));
          } else {
            die('Motor de conexión no identificado.');
          }
        break;
      }

    } catch (PDOException $e) {
      if(IS_API) {
        die(json_encode(array('success' => 0, 'message' => 'Error intentando conectar con la base de datos.')));
      } else {
        die('Error intentando conectar con la base de datos.');
      }
    } finally {
      unset($MOTOR,$DATABASE);
    }
  }

  //------------------------------------------------

  /**
    * Devuelve un arreglo asociativo de todos los resultados arrojados por una query
    *
    * @param object PDOStatement $query, valor devuelto de la query
    *
    * @return arreglo asociativo
  */
  final public function fetch_array(PDOStatement $query) : array {
    return $query->fetchAll(PDO::FETCH_ASSOC);
  }

  //------------------------------------------------

  /**
    * Consigue el numero de filas encontradas después de un SELECT
    *
    * @param object PDOStatement $query, valor devuelto de la query
    *
    * @return numero de filas encontradas
  */
  final public function rows(PDOStatement $query) : int {
    return $query->rowCount();
  }

  //------------------------------------------------

  /**
    * Sana un valor para posteriormente ser introducido en una query
    *
    * @param string/int/float a sanar
    *
    * @return int/float/string sanados según sea el tipo de dato pasado por parámetro
  */
  final public function scape($e) {
    if(!isset($e)) {
      return '';
    }

    if(is_numeric($e) and $e <= 2147483647) {
      if(explode('.',$e)[0] != $e) {
        return (float) $e;
      }
      return (int) $e;
    }

    return (string) trim(str_replace(['\\',"\x00",'\n','\r',"'",'"',"\x1a"],['\\\\','\\0','\\n','\\r',"\'",'\"','\\Z'],$e));
  }

  //------------------------------------------------

  /**
    * Realiza una query, y si está en modo debug analiza que query fue ejecutada
    *
    * @param SQL string, recibe la consulta SQL a ejecutar
    *
    * @return object PDOStatement
  */
  final public function query(string $q) : PDOStatement {
    try {

      if(DEBUG) {
        $_SESSION['___QUERY_DEBUG___'][] = (string) $q;
      }

      return parent::query($q);
    } catch (Exception $e) {
      if(defined(GENERATOR)) {
        $message = "\nError en la query:\n $q \n\n" . $e->getMessage();
      } else {
        $message = 'Error en la query: <b>' . $q . '<b/><br /><br />' . $e->getMessage();
      }
      die(IS_API ? json_encode(array('success' => 0, 'message' => $message)) : $message);
    }
  }

  //------------------------------------------------

  /**
    * Borra una serie de elementos de forma segura de una tabla en la base de datos
    *
    * @param string $table: Tabla a la cual se le quiere remover un elemento
    * @param string $where: Condición de borrado que define quien/quienes son dichos elementos
    * @param string $limit: Por defecto se limita a borrar un solo elemento que cumpla el $where
    *
    * @return object PDOStatement
  */
  final public function delete(string $table, string $where, string $limit = 'LIMIT 1') : PDOStatement {
    return $this->query("DELETE FROM $table WHERE $where $limit;");
  }

  //------------------------------------------------

  /**
    * Inserta una serie de elementos a una tabla en la base de datos
    *
    * @param string $table: Tabla a la cual se le va a insertar elementos
    * @param array $e: Arreglo asociativo de elementos, con la estrctura 'campo_en_la_tabla' => 'valor_a_insertar_en_ese_campo',
    *                  todos los elementos del arreglo $e, serán sanados por el método sin necesidad de hacerlo manualmente al crear el arreglo
    *
    * @return object PDOStatement
  */
  final public function insert(string $table, array $e) : PDOStatement {
    if (sizeof($e) == 0) {
      trigger_error('El arreglo pasado por $this->db->insert(...) está vacío.', E_ERROR);
    }

    $query = "INSERT INTO $table (";
    $values = '';
    foreach ($e as $campo => $v) {
      $query .= $campo . ',';
      $values .= '\'' . $this->scape($v) . '\',';
    }
    $query[strlen($query) - 1] = ')';
    $values[strlen($values) - 1] = ')';
    $query .= ' VALUES (' . $values . ';';

    return $this->query($query);
  }

  //------------------------------------------------

  /**
    * Actualiza elementos de una tabla en la base de datos según una condición
    *
    * @param string $table: Tabla a actualizar
    * @param array $e: Arreglo asociativo de elementos, con la estrctura 'campo_en_la_tabla' => 'valor_a_insertar_en_ese_campo',
    *                  todos los elementos del arreglo $e, serán sanados por el método sin necesidad de hacerlo manualmente al crear el arreglo
    * @param string $where: Condición que indica quienes serán modificados
    * @param string $limite: Límite de elementos modificados, por defecto los modifica a todos
    *
    * @return object PDOStatement
  */
  final public function update(string $table, array $e, string $where, string $limit = '') : PDOStatement {
    if (sizeof($e) == 0) {
      trigger_error('El arreglo pasado por $this->db->update(...) está vacío.', E_ERROR);
    }

    $query = "UPDATE $table SET ";
    foreach ($e as $campo => $valor) {
      $query .= $campo . '=\'' . $this->scape($valor) . '\',';
    }
    $query[strlen($query) - 1] = ' ';
    $query .= "WHERE $where $limit;";

    return $this->query($query);
  }

  //------------------------------------------------

  /**
    * Selecciona y lista en un arreglo asociativo/numérico los resultados de una búsqueda en la base de datos
    *
    * @param string $e: Elementos a seleccionar separados por coma
    * @param string $tbale: Tabla de la cual se quiere extraer los elementos $e
    * @param string $where: Condición que indica quienes son los que se extraen, si no se coloca extrae todos
    * @param string $limite: Límite de elemntos a traer, por defecto trae TODOS los que cumplan $where
    *
    * @return false si no encuentra ningún resultado, array asociativo/numérico si consigue al menos uno
  */
  final public function select(string $e, string $table, string $where = '1 = 1', string $limit = "") {
    $sql = $this->query("SELECT $e FROM $table WHERE $where $limit;");
    $result = $sql->fetchAll();
    $sql->closeCursor();

    if(sizeof($result) > 0) {
      return $result;
    }

    return false;
  }

  //------------------------------------------------

  /**
    * Alert para evitar clonaciones
    *
    * @return void
  */
  final public function __clone() {
    trigger_error('Estás intentando clonar la Conexión', E_ERROR);
  }

}

?>
