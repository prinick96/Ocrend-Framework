<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ocrend\Kernel\Models\Traits;

use Ocrend\Kernel\Database\Database;
 
/**
 * Añade características a un modelo para que pueda conectarse a una base de datos.
 *
 * @author Brayan Narváez <prinick@ocrend.com>
*/

trait DBModel {

    /**
     * Tiene la instancia de la base de datos actual
     *
     * @var null|Mysql|Sqlite;
     */
    protected $db = null;

    /**
     * Constructor inicial del modelo.
     * Arranca la base de datos
     * 
     * @param $driver : Driver para establecer la conexión de este modelo
     * 
     * @return void
     */
    protected function startDBConexion($driver = null)  {
        global $config;
        $this->db = Database::resolveDriver($driver ?? $config['database']['default_driver']);
    }
}