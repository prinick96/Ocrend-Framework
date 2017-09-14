<?php

/*
 * This file is part of the Ocrend Framewok 2 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ocrend\Kernel\Models;

/**
 * Excepción producida en un modelo, para controlar la salida del error desde la api/controller.
 *
 * @author Brayan Narváez <prinick@ocrend.com>
 */

class ModelsException extends \Exception {

    /**
     * __construct()
     */
    public function __construct($message = null, $code = 1, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Muestra el error con un formato u otro dependiendo desde donde se hace la petición.
     */
    public function errorResponse() {
        throw new \RuntimeException($this->getMessage());
    }

}