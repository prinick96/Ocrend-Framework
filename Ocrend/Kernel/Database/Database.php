<?php

/*
 * This file is part of the Ocrend Framewok 2 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ocrend\Kernel\Database;

/**
 * Clase para conectar todos los modelos del sistema y compartir la configuración.
 * Inicializa elementos escenciales como la conexión con la base de datos.
 *
 * @author Brayan Narváez <prinick@ocrend.com>
 */

  class Database extends \PDO {

    /**
     * Contiene la instancia de conexión a la base de datos
     * 
     * @var Database
     */
    private static $inst;

  /**
   * Inicia la instancia de conexión, si esta ya ha sido declarada antes, no la duplica y ahorra memoria.
   *
   * @param string|null $name : Nombre de la base de datos a conectar
   * @param string|null $motor: Motor de la base de datos a conectar
   * @param bool $new_instance: true para iniciar una nueva instancia (al querer conectar a una DB distinta)
   *
   * @return Database : Instancia de conexión
   */
  final public static function Start($name = null, $motor = null, bool $new_instance = false) : Database {
    global $config;

    if (!self::$inst instanceof self or $new_instance) {
      self::$inst = new self(
          null === $name ? $config['database']['name'] : $name,
          null === $motor ? $config['database']['motor'] : $motor
        );
    }

    return self::$inst;
  }

  /**
    * Inicia la conexión con la base de datos seleccionada
    *
    * @param string|null $name : Nombre de la base de datos a conectar
    * @param string|null $motor: Motor de la base de datos a conectar
    * @param bool $new_instance: true para iniciar una nueva instancia (al querer conectar a una DB distinta)
    *
    * @throws \RuntimeException si el motor no existe
    * @throws \RuntimeException si existe algún problema de conexión con la base de datos
    * @return Database : Instancia de conexión
  */
  final public function __construct(string $name, string $motor) {
    global $config;

    # Configuración común
    $comun_config = array(
      \PDO::ATTR_EMULATE_PREPARES => false,
      \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
    );

    try {
      switch ($motor) {
        case 'sqlite':
          parent::__construct('sqlite:'.$name);
        break;
        case 'cubrid':
          parent::__construct('cubrid:host='.$config['database']['host'].';dbname='.$name.';port='.$config['database']['port'],
          $config['database']['user'],
          $config['database']['pass'],
          $comun_config);
        break;
        case 'firebird':
          parent::__construct('firebird:dbname='.$config['database']['host'].':'.$name,
          $config['database']['user'],
          $config['database']['pass'],
          $comun_config);
        break;
        case 'odbc';
          parent::__construct('odbc:'.$name,
          $config['database']['user'],
          $config['database']['pass']);
        break;
        case 'oracle':
          parent::__construct('oci:dbname=(DESCRIPTION =
            (ADDRESS_LIST =
              (ADDRESS = (PROTOCOL = '.$config['database']['protocol'].')(HOST = '.$motor.')(PORT = '.$config['database']['port'].'))
            )
            (CONNECT_DATA =
              (SERVICE_NAME = '.$name.')
            )
          );charset=utf8',
          $config['database']['user'],
          $config['database']['pass'],
          array_merge(array(\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC),$comun_config));
        break;
        case 'postgresql':
          parent::__construct('pgsql:host='.$config['database']['host'].';dbname='.$name.';charset=utf8',
          $config['database']['user'],
          $config['database']['pass'],
          $comun_config);
        break;
        case 'mysql':
          parent::__construct('mysql:host='.$config['database']['host'].';dbname='.$name,
          $config['database']['user'],
          $config['database']['pass'],
          array_merge(array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'),$comun_config));
        break;
        case 'mssql':
          # Añadido por marc2684
          # DOC: https://github.com/prinick96/Ocrend-Framework/issues/7
          parent::__construct('sqlsrv:Server=' . $config['database']['host'] . ';Database=' . $name . ';ConnectionPooling=0',
          $config['database']['user'],
          $config['database']['pass'],
          $comun_config);
        break;
        default:
          throw new \RuntimeException('Motor ' . $motor . ' de conexión no identificado.');
        break;
      }
    } catch (\PDOException $e) {
      throw new \RuntimeException('Problema al conectar con la base de datos: ' . $e->getMessage());
    } 
  }

  /**
    * Convierte en arreglo asociativo de todos los resultados arrojados por una query
    *
    * @param object \PDOStatement $query, valor devuelto de la query
    *
    * @return array arreglo asociativo
  */
  final public function fetch_array(\PDOStatement $query) : array {
    return $query->fetchAll(\PDO::FETCH_ASSOC);
  }

   /**
    * Cantidad de filas encontradas después de un SELECT
    *
    * @param object \PDOStatement $query, valor devuelto de la query
    *
    * @return int numero de filas encontradas
  */
  final public function rows(\PDOStatement $query) : int {
    return $query->rowCount();
  }

  /**
    * Sana un valor para posteriormente ser introducido en una query
    *
    * @param null/string/int/float a sanar
    *
    * @return mixed elemento sano
  */
  final public function scape($e) {
    if (null === $e) {
      return '';
    }

    if (is_numeric($e) and $e <= 2147483647) {
      if (explode('.', $e)[0] != $e) {
        return (float) $e;
      }
      return (int) $e;
    }

    return (string) trim(str_replace(['\\', "\x00", '\n', '\r', "'", '"', "\x1a"], ['\\\\', '\\0', '\\n', '\\r', "\'", '\"', '\\Z'], $e));
  }

  /**
    * Borra una serie de elementos de forma segura de una tabla en la base de datos
    *
    * @param string $table: Tabla a la cual se le quiere remover un elemento
    * @param string $where: Condición de borrado que define quien/quienes son dichos elementos
    * @param string $limit: Por defecto se limita a borrar un solo elemento que cumpla el $where
    *
    * @return \PDOStatement
  */
  final public function delete(string $table, string $where, string $limit = 'LIMIT 1') : \PDOStatement {
    return $this->query("DELETE FROM $table WHERE $where $limit;");
  }

  /**
    * Inserta una serie de elementos a una tabla en la base de datos
    *
    * @param string $table: Tabla a la cual se le va a insertar elementos
    * @param array $e: Arreglo asociativo de elementos, con la estrctura 'campo_en_la_tabla' => 'valor_a_insertar_en_ese_campo',
    *                  todos los elementos del arreglo $e, serán sanados por el método sin necesidad de hacerlo manualmente al crear el arreglo
    *
    * @throws \RuntimeException si el arreglo está vacío
    * @return \PDOStatement
  */
  final public function insert(string $table, array $e) : \PDOStatement {
      if (sizeof($e) == 0) {
          throw new \RuntimeException('El arreglo pasado por $this->db->insert(\'' . $table . '\',...) está vacío.');
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

  /**
    * Actualiza elementos de una tabla en la base de datos según una condición
    *
    * @param string $table: Tabla a actualizar
    * @param array $e: Arreglo asociativo de elementos, con la estrctura 'campo_en_la_tabla' => 'valor_a_insertar_en_ese_campo',
    *                  todos los elementos del arreglo $e, serán sanados por el método sin necesidad de hacerlo manualmente al crear el arreglo
    * @param string $where: Condición que indica quienes serán modificados
    * @param string $limite: Límite de elementos modificados, por defecto los modifica a todos
    *
    * @throws \RuntimeException si el arreglo está vacío
    * @return \PDOStatement
  */
  final public function update(string $table, array $e, string $where, string $limit = '') : \PDOStatement {
      if (sizeof($e) == 0) {
          throw new \RuntimeException('El arreglo pasado por $this->db->update(\'' . $table . '\'...) está vacío.');
      }

      $query = "UPDATE $table SET ";
      foreach ($e as $campo => $valor) {
          $query .= $campo . '=\'' . $this->scape($valor) . '\',';
      }
      $query[strlen($query) - 1] = ' ';
      $query .= "WHERE $where $limit;";

      return $this->query($query);
  }

  /**
    * Selecciona y lista en un arreglo asociativo/numérico los resultados de una búsqueda en la base de datos
    *
    * @param string $e: Elementos a seleccionar separados por coma
    * @param string $tbale: Tabla de la cual se quiere extraer los elementos $e
    * @param string $where: Condición que indica quienes son los que se extraen, si no se coloca extrae todos
    * @param string $limite: Límite de elemntos a traer, por defecto trae TODOS los que cumplan $where
    *
    * @return mixed false si no encuentra ningún resultado, array asociativo/numérico si consigue al menos uno
  */
  final public function select(string $e, string $table, string $where = '1 = 1', string $limit = "") {    
    return $this->query_select("SELECT $e FROM $table WHERE $where $limit;");
  }

   /**
    * Realiza una query, ideal para trabajar con SELECTS, JOINS, etc
    *
    * @return array|false si no encuentra ningún resultado, array asociativo/numérico si consigue al menos uno
  */
  final public function query_select(string $query) {
    $sql = $this->query($query);
    $result = $sql->fetchAll();
    $sql->closeCursor();

    if (sizeof($result) > 0) {
      return $result;
    }

    return false;
  }

  /**
    * Alert para evitar clonaciones
    *
    * @throws \RuntimeException si se intenta clonar la conexión
    * @return void
  */
  final public function __clone() {
    throw new \RuntimeException('Estás intentando clonar la Conexión');
  }

 }