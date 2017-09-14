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
      * Contiene una instancia del helper para funciones
      *
      * @var \Ocrend\Kernel\Helpers\Functions
    */
    protected $functions;

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
        global $session, $config;
        
        # Id captado por la ruta
        if (null != $router) {
            $this->id = $router->getId(true);
            $this->id = null == $this->id ? 0 : $this->id; 
        }

        # Instanciar las funciones
        $this->functions = new Functions();

        # Verificar sesión del usuario
        if(null !== $session->get('user_id') && $session->get('unique_session') == $config['sessions']['unique']) {
           $this->id_user = $session->get('user_id');
        }
    }

    /**
      * Asigna el id desde un modelo, ideal para cuando queremos darle un valor numérico 
      * que proviene de un formulario y puede ser inseguro.
      *
      * @param mixed $id : Id a asignar en $this->id
      * @param string $default_msg : Mensaje a mostrar en caso de que no se pueda asignar
      *
      * @throws ModelsException
      */
    protected function setId($id, string $default_msg = 'No puedede asignarse el id.') {
        if (null == $id || !is_numeric($id) || $id <= 0) {
            throw new ModelsException($default_msg);
        }

        $this->id = (int) $id;
    }

    /**
     * Cierra el modelo
     */
    protected function __destruct() {}

}