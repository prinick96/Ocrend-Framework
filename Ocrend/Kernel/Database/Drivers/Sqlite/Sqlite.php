<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ocrend\Kernel\Database\Drivers\Sqlite;

use Ocrend\Kernel\Database\Driver; 

/**
 * Driver de conexión con Sqlite utilizando PDO
 * 
 * @author Brayan Narváez <prinick@ocrend.com>
 */
class Sqlite extends \PDO implements Driver {

    /**
     * Constructor de la clase
     */
    public function __construct() {
        global $config;

        # Configuración
        $sqlite3 = $config['database']['drivers']['sqlite3'];

        # Establecer conexión, si la base de datos no existe, es creada
        parent::__construct('sqlite:' . str_replace('___ROOT___',___ROOT___,$sqlite3['file']));
    }

    /**
     * Escapa caracteres para evitar sql injection
     * 
     * @param string $param : Parámetro
     * 
     * @return string escapado
     */
    public function scape($param) : string {
        if (null === $param) {
            return '';
        }
        if (is_numeric($param) and $param <= 2147483647) {
            if (explode('.', $param)[0] != $param) {
                return (string) $param;
            }
            return (string) $param;
        }
        return (string) trim(str_replace(['\\', "\x00", '\n', '\r', "'", '"', "\x1a"], ['\\\\', '\\0', '\\n', '\\r', "\'", '\"', '\\Z'], $param));
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

        if(false != $result && sizeof($result)) {
            $matriz = (array) $result->fetchAll(PDO::FETCH_ASSOC);
            $result->closeCursor();

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

        return $this->exec($query
        . (null != $where ? "WHERE $where" : '') 
        . (null !== $limit ? "LIMIT $limit" : '')
        );
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

        $this->exec($query);

        return $this->lastInsertId();  
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
        return $this->exec("DELETE FROM $table " . (null != $where ? "WHERE $where" : ' ') . (null !== $limit ? "LIMIT $limit" : ''));
    }

    /**
     * __destruct()
     */
    public function __destruct() {}
}