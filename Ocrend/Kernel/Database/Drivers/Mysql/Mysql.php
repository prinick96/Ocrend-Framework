<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ocrend\Kernel\Database\Drivers\Mysql;

use Ocrend\Kernel\Database\Driver; 

/**
 * Driver de conexión con Mysql utilizando mysqli
 * 
 * @author Brayan Narváez <prinick@ocrend.com>
 */
class Mysql extends \mysqli implements Driver {

    /**
     * Constructor de la clase
     */
    public function __construct() {
        global $config;

        # Configuración
        $mysqli = $config['database']['drivers']['mysql'];

        # Puerto y socket por defecto
        $port = ini_get('mysqli.default_port');
        $socket = ini_get('mysqli.default_socket');

        # Conexión
        parent::__construct(
            $mysqli['host'],
            $mysqli['user'],
            $mysqli['pass'],
            $mysqli['name'],
            $mysqli['port'] == 'default' ? $port : $mysqli['port'],
            $mysqli['socket'] == 'default' ? $socket : $mysqli['socket']
        );

        # Verificar conexión
        if($this->connect_errno) {
            throw new \RuntimeException('ERROR :' . $this->connect_errno . ' conectando con la base de datos' . $this->connect_error);
        }

        # Cambiar caracteres
        $this->set_charset("utf8mb4");
    }

    /**
     * Escapa caracteres para evitar sql injection
     * 
     * @param string $param : Parámetro
     * 
     * @return string escapado
     */
    public function scape($param) : string {
        return $this->real_escape_string($param);
    }

    /**
     * Selecciona elementos de una tabla y devuelve un objeto
     * 
     * @param string $fields: Campos
     * @param string $table: Tabla
     * @param null|string $inners: Inners
     * @param null|string $where : Condiciones
     * @param null|int $limit: Límite de resultados
     * @param string $extra: Instrucciones extras
     * 
     * @return bool|stdClass
     */
    public function select(string $fields, string $table, $inners = null, $where = null, $limit = null, string $extra = '') {
        $result = $this->query("SELECT $fields FROM $table $inners "
        . (null != $where ? "WHERE $where" : '') 
        . " $extra " 
        . (null !== $limit ? "LIMIT $limit" : '')
        );

        if(false != $result && $result->num_rows) {
            $matriz = (array) $result->fetch_all(MYSQLI_ASSOC);
            $result->free();

            return $matriz;
        }

        return false;
    }

    /**
     * Actualiza elementos de una tabla en la base de datos según una condición
     *
     * @param string $table: Tabla a actualizar
     * @param array $e: Arreglo asociativo de elementos, con la estrctura 'campo_en_la_tabla' => 'valor_a_insertar_en_ese_campo',
     *                  todos los elementos del arreglo $e, serán sanados por el método sin necesidad de hacerlo manualmente al crear el arreglo
     * @param null|string $where: Condición que indica quienes serán modificados
     * @param null|string $limite: Límite de elementos modificados, por defecto los modifica a todos
     *
     * @throws \RuntimeException si el arreglo está vacío
     * @return int con la cantidad de tablas afectadas
    */
    public function update(string $table, array $e, $where = null, $limit = null) : int {
        if (sizeof($e) == 0) {
            throw new \RuntimeException('El arreglo pasado por $this->db->update(\'' . $table . '\'...) está vacío.');
        }

        $query = "UPDATE $table SET ";
        foreach ($e as $campo => $valor) {
            $query .= $campo . '=\'' . $this->scape($valor) . '\',';
        }
        $query[strlen($query) - 1] = ' ';

        $this->real_query($query
        . (null != $where ? "WHERE $where" : '') 
        . (null !== $limit ? "LIMIT $limit" : '')
        );

        return $this->affected_rows;
    }

    /**
     * Inserta una serie de elementos a una tabla en la base de datos
     *
     * @param string $table: Tabla a la cual se le va a insertar elementos
     * @param array $e: Arreglo asociativo de elementos, con la estrctura 'campo_en_la_tabla' => 'valor_a_insertar_en_ese_campo',
     *                  todos los elementos del arreglo $e, serán sanados por el método sin necesidad de hacerlo manualmente al crear el arreglo
     *
     * @throws \RuntimeException si el arreglo está vacío
     * 
     * @return int con el PRIMARY AUTO_INCREMENT de el último elemento insertado
     */
    public function insert(string $table, array $e) : int {
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

        $this->real_query($query);

        return $this->insert_id;  
    }

    /**
     * Elimina elementos de una tabla y devuelve la cantidad de filas afectadas
     * 
     * @param string $table: Tabla a la cual se le quiere remover un elemento
     * @param null|string $where: Condición de borrado que define quien/quienes son dichos elementos
     * @param null|string $limit: Por defecto se limita a borrar un solo elemento que cumpla el $where
     * 
     * @return int cantidad de filas afectadas
     */
    public function delete(string $table, $where = null, $limit = null) : int {
        $this->real_query("DELETE FROM $table " . (null != $where ? "WHERE $where" : ' ') . (null !== $limit ? "LIMIT $limit" : ''));
        
        return $this->affected_rows;
    }

    /**
     * Destructor de la clase
     */
    public function __destruct() {
        $this->close();
    }

}
