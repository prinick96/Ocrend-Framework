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
use  Ocrend\Kernel\Database\Drivers\Mysql\Mysql;
use  Ocrend\Kernel\Database\Drivers\Sqlite\Sqlite;

/**
 * Clase para conectar todos los modelos del sistema y compartir la configuración.
 * Inicializa elementos escenciales como la conexión con la base de datos.
 *
 * @author Brayan Narváez <prinick@ocrend.com>
 */
class Database {
    
    /**
     * Resuelve el controlador de base de datos solicitado
     * 
     * @param string $motor: Motor a conectar
     * 
     * @return Driver
     */
    public static function resolveDriver(string $motor) : Driver {
        global $config;

        if($motor == 'mysql') {
            return new Mysql;
        } 

        return new Sqlite;
    }
}