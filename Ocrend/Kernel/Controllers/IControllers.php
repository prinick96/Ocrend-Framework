<?php

/*
 * This file is part of the Ocrend Framewok 2 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ocrend\Kernel\Controllers;

use Ocrend\Kernel\Router\IRouter;

/**
 * Estructura elemental para el correcto funcionamiento de cualquier controlador en el sistema.    
 *
 * @author Brayan Narváez <prinick@ocrend.com>
 */
interface IControllers {
    public function __construct(IRouter $router);
}