<?php

/*
 * This file is part of the Ocrend Framewok 2 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ocrend\Kernel\Config;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * Inicializa la configuración del sistema.
 *
 * @author Brayan Narváez <prinick@ocrend.com>
 */

final class Config {

    /**
     * Ruta con parámetros de configuración
     *
     * @var string   
     */
    const FILE_CONFIG_ROUTE = API_INTERFACE . 'Ocrend/Kernel/Config/Ocrend.ini.yml';

    /**
     * Lee la configuración del archivo self::FILE_CONFIG_ROUTE 
     *
     * @return array : Arreglo con la configuración en el archivo .yml
     */
    final public function readConfig() : array {
        return Yaml::parse(file_get_contents(self::FILE_CONFIG_ROUTE));
    }

}