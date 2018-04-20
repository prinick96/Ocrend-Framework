<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ocrend\Kernel\Models;

use Ocrend\Kernel\Router\IRouter;
use Ocrend\Kernel\Helpers\Functions;

/**
 * Clase para conectar todos los modelos del sistema y compartir la configuración.
 *
 * @author Brayan Narváez <prinick@ocrend.com>
 */
abstract class Models {

    /**
      * Tiene siempre el id pasado por la ruta, en caso de no haber ninguno, será cero.
      *
      * @var int 
    */
    protected $id = 0;

    /**
      * Contiene el id del usuario que tiene su sesión iniciada.
      *
      * @var int|null con id del usuario
    */
    protected $id_user = null;

    /**
      * Inicia la configuración inicial de cualquier modelo
      *
      * @param IRouter $router: Instancia de un Router 
      *                                    
    */
    protected function __construct(IRouter $router = null) {
        global $session, $cookie;
        
        # Id captado por la ruta
        if (null != $router) {
            $this->id = $router->getId(true);
            $this->id = null == $this->id ? 0 : $this->id; 
        }
        
        # Verificar sesión del usuario
        $session_name = $cookie->get('session_hash') . '__user_id';
        if(null !== $session->get($session_name)) {
           $this->id_user = $session->get($session_name);
        }
    }
}
