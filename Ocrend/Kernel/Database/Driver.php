<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ocrend\Kernel\Database;

/**
 * Interfaz para los drivers
 * 
 * @author Brayan Narváez <prinick@ocrend.com>
 */
interface Driver {
    public function __construct();
    public function scape($param) : string;
    public function select(string $fields, string $table, $inners = null, $where = null, $limit = null, string $extra = '');
    public function update(string $table, array $e, $where = null, $limit = null) : int;
    public function insert(string $table, array $e) : int;
    public function delete(string $table, $where = null, $limit = null) : int;
    public function __destruct();
}