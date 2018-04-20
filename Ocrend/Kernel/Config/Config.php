<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 * @author Brayan Narváez <prinick@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;
use Ocrend\Kernel\Cookies\Cookies;

# Cargadores iniciales
require ___ROOT___ . 'Ocrend/vendor/autoload.php';
require ___ROOT___ . 'Ocrend/autoload.php';

# Manejador de excepciones
ErrorHandler::register();
ExceptionHandler::register();  

# Mínima versión, alerta
if (version_compare(phpversion(), '7.0.0', '<')) {
    throw new \RuntimeException('La versión actual de PHP es ' . phpversion() . ' y como mínimo se require la versión 7.0.0');
}

# Verificar usabilidad de twig
$__TWIG_CACHE_PATH = ___ROOT___ . 'app/templates/.compiled/';
$__TWIG_READABLE_AND_WRITABLE = !is_readable($__TWIG_CACHE_PATH) || !is_writable($__TWIG_CACHE_PATH);
if ($__TWIG_READABLE_AND_WRITABLE) {

    # Intentar solucionarlo
    if(!is_dir($__TWIG_CACHE_PATH)) {
        mkdir($__TWIG_CACHE_PATH, 0644, true);
    } else {
        chmod($__TWIG_CACHE_PATH, 0644);
    }

    # Revisar la lecutra para twig
    if($__TWIG_READABLE_AND_WRITABLE) {
        throw new \RuntimeException('Debe conceder permisos de escritura y lectura a la ruta '. $__TWIG_CACHE_PATH .' ó crearla si no existe.');
    }
}

# Obtener la data informativa
$config = Yaml::parse(file_get_contents(___ROOT___ . 'Ocrend/Kernel/Config/Ocrend.ini.yml'));

# Cargador de sesiones
$session = new Session(new NativeSessionStorage(
    array(
      'cookie_lifetime' => $config['sessions']['lifetime']
    )
));
$session->start();

# Cargador de cookies
$cookie = new Cookies;
$cookie->reviveSessions();

# Peticiones HTTP
$http = Request::createFromGlobals(); 

# Define el timezone actual
date_default_timezone_set($config['build']['timezone']);