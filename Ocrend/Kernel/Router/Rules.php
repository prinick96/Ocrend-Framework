<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ocrend\Kernel\Router;

/**
 * Encargado de manejar las reglas definidas en Router::RULES 
 *
 * @author Brayan Narváez <prinick@ocrend.com>
*/
final class Rules {

    /**
     * Sin ninguna regla
     *
     * @param mixed $ruta : Ruta a aplicar la regla
     *
     * @return mixed
    */
    final public function none($ruta) {
        return $ruta;
    }

    /**
     * Sólamente letras
     *
     * @param mixed $ruta : Ruta a aplicar la regla
     *
     * @return mixed
    */    
    final public function letters($ruta) {
        return preg_match("/^[a-zA-Z ]*$/", $ruta) ? $ruta : null;
    }

    /**
     * Letras y números
     *
     * @param mixed $ruta : Ruta a aplicar la regla
     *
     * @return mixed
    */   
    final public function alphanumeric($ruta) {
        return preg_match('/^[a-zA-Z0-9 ]*$/', $ruta) ? $ruta : null;
    }

    /**
     * Con forma para URL (letras,números y el caracter -)
     *
     * @param mixed $ruta : Ruta a aplicar la regla
     *
     * @return mixed
    */
    final public function url($ruta) {
        return preg_match('/^[a-zA-Z0-9- ]*$/', $ruta) ? $ruta : null;
    }

    /**
     * Sólamente números enteros
     *
     * @param mixed $ruta : Ruta a aplicar la regla
     *
     * @return int|null
    */
    final public function integer($ruta) {
        return is_numeric($ruta) ? (int) $ruta : null;
    }

    /**
     * Solamente números enteros positivos
     *
     * @param mixed $ruta : Ruta a aplicar la regla
     *
     * @return int|null
    */    
    final public function integer_positive($ruta) {
        return is_numeric($ruta) && $ruta >= 0 ? (int) $ruta : null;
    }

    /**
     * Solamente números con decimal
     *
     * @param mixed $ruta : Ruta a aplicar la regla
     *
     * @return float|null
    */
    final public function float($ruta) {
        return is_numeric($ruta) ? (float) $ruta : null;
    }

    /**
     * Solamente números con decimal y positivos
     *
     * @param mixed $ruta : Ruta a aplicar la regla
     *
     * @return float|null
    */
    final public function float_positive($ruta) {
        return is_numeric($ruta) && $ruta >= 0 ? (float) $ruta : null;
    }
}