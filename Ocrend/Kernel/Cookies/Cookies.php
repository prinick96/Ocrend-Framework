<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ocrend\Kernel\Cookies;
use Ocrend\Kernel\Helpers as Helper;

/**
 * Implementación de cookies con POO
 *
 * @author Brayan Narváez <prinick@ocrend.com>
 */
class Cookies {

    /**
     * Establece una cookie
     * 
     * @param string $cookie : Nombre
     * @param mixed $value : Valor
     * @param int $time: Tiempo de vida
     * @param string $path : Path de creación
     * 
     * @return void
     */
    public function set(string $cookie, $value, int $time, string $path = '/') {
        setcookie($cookie, $value, time() + $time, $path);
    }

    /**
     * Obtiene una cookie
     * 
     * @param string $cookie : Nombre
     * 
     * @return mixed
     */
    public function get(string $cookie) {
        if (isset($_COOKIE[$cookie])) { 
            return $_COOKIE[$cookie];
        }

        return null;
    }

    /**
     * Obtiene todas las cookies
     * 
     * @return array
     */
    public function all() : array {
        return $_COOKIE;
    }

    /**
     * Elimina una cookie
     * 
     * @param string $cookie : Nombre
     * 
     * @return void
     */
    public function remove(string $cookie) {
        if (isset($_COOKIE[$cookie])) {
            unset($_COOKIE[$cookie]);
            setcookie($cookie, null, -1, '/');
        }
    }

    /**
     * Reestablece la sesión del usuario, en caso de que sea necesario
     * 
     * @return void
     */
    public function reviveSessions() {
        global $session, $config;

        # Verificar si está habilitada la opción de sesión de usuario con cookie
        if($config['sessions']['user_cookie']['enable']) {
            # Obtener
            $user_session = $session->get($this->get('session_hash') . '__user_id');
            $salt = $this->get('appsalt');
            $encrypt = $this->get('appencrypt');

            # Verificar que no estén vacías y coincidan
            if(null == $user_session && null != $salt && null != $encrypt && Helper\Strings::chash($salt ?? '_', $encrypt ?? '/$')) {
                # Generar un nuevo session hash
                $this->set('session_hash', md5(time()) , $config['sessions']['user_cookie']['lifetime']);

                # Desencriptar el id del usuario
                $id_user = Helper\Strings::ocrend_decode($encrypt, $config['sessions']['user_cookie']['key_encrypt']);

                # Reestablecer la sesión
                $session->set($this->get('session_hash') . '__user_id',(int) $id_user);
            }
        }       
    }
}
