<?php

/*
 * This file is part of the Ocrend Framewok 2 package.
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
     * @var null|Database
     */
    protected $db = null;

    /**
     * Contiene la información que se pasa al manejador de la base de datos. 
     * - Nombre de base de datos
     * - Motor de base de datos 
     * - Valor de nueva instancia
     *
     * @var array
     */
    private $databaseConfig = array();

    /**
     * Establece la configuración de la base de datos
     *
     * @param array|null $databaseConfig: Configuración de conexión con base de datos con la forma
     *     'name' => string, # Nombre de la base de datos
     *     'motor' => string, # Motor de la base de datos
     *     'new_instance' => bool, # Establecer nueva instancia distinta a alguna ya existente
     */
    private function setDatabaseConfig($databaseConfig) {
        global $config;

        # Parámetros por defecto
        $this->databaseConfig['name'] = $config['database']['name'];
        $this->databaseConfig['motor'] = $config['database']['motor'];
        $this->databaseConfig['new_instance'] = false;

        # Añadir según lo pasado por $databaseConfig
        if(is_array($databaseConfig)) {
            if(array_key_exists('name',$databaseConfig)) {
                $this->databaseConfig['name'] =  $databaseConfig['name'];
            } 

            if(array_key_exists('motor',$databaseConfig)) {
                $this->databaseConfig['motor'] =  $databaseConfig['motor'];
            } 

            if(array_key_exists('new_instance',$databaseConfig)) {
                $this->databaseConfig['new_instance'] = (bool) $databaseConfig['new_instance'];
            }
        }
    }

    /**
     * Constructor inicial del modelo.
     *
     * @param array|null $databaseConfig: Configuración de conexión con base de datos con la forma
     *     'name' => string, # Nombre de la base de datos
     *     'motor' => string, # Motor de la base de datos
     *     'new_instance' => bool, # Establecer nueva instancia distinta a alguna ya existente
     */
    protected function startDBConexion($databaseConfig = null) {
        # Llenar la configuración a la base de datos
        $this->setDatabaseConfig($databaseConfig);

        # Instancia a la base de datos 
        $this->db = Database::Start(
            $this->databaseConfig['name'],
            $this->databaseConfig['motor'],
            $this->databaseConfig['new_instance']
        );
    }

    /**
     * Finaliza la conexión con la base de datos.
     */
    protected function endDBConexion() {
        $this->db = null;
    }
}