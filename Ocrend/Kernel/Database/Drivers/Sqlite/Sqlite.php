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
 * Driver de conexión con Sqlite utilizando SQLite3
 * 
 * @author Brayan Narváez <prinick@ocrend.com>
 */
class Sqlite extends \SQLite3 implements Driver {

    public function __construct() {
        parent::__construct('_RUTA AQUI_', SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE, ' encryption_key AQUI');
    }

    public function scape($param) : string {
        return '';
    }

    public function select(string $fields, string $table, $where = null, $limit = null, string $extra = '') {

    }

    public function update(string $table, array $e, $where = null, $limit = null) : int {
        return 1;
    }

    public function insert(string $table, array $e) : int {
        return 1;
    }

    public function delete(string $table, string $where, $limit = null) : int {
        return 1;
    }

    public function __destruct() {

    }
}