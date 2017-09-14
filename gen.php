<?php

/*
 * This file is part of the Ocrend Framewok 2 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Ocrend\Kernel\Generator as PHPGen;

# Definir ruta de acceso permitida
define('API_INTERFACE', '');
define('GENERATOR', true);

# Conexión con el framework
require 'Ocrend/vendor/autoload.php';
require 'Ocrend/autoload.php';
require 'Ocrend/Kernel/Config/Start.php';

# Lanzar el generador
new PHPGen\Generator($argv);