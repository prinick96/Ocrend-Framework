<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

use Ocrend\Kernel\Router\Router;

# Definir el path
define('___ROOT___', '');

# Iniciar la configuración
require ___ROOT___ . 'Ocrend/Kernel/Config/Config.php';

# Ejecutar controlador solicitado
(new Router)->executeController();