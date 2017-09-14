<?php

/*
 * This file is part of the Ocrend Framewok 2 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 * @author Brayan Narváez <prinick@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Ocrend\Kernel\Config\Config;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\Debug;

/**
 * Lanzador inicial del sistema.
 *
 * @author Brayan Narváez <prinick@ocrend.com>
*/

//---------------------------------------------------------------------------------------------

/**
 * Manejador de excepciones y errores
*/
ErrorHandler::register();
ExceptionHandler::register();  

//---------------------------------------------------------------------------------------------

/**
 * Versión mínima de PHP
*/
if (version_compare(phpversion(), '7.0.0', '<')) {
  throw new \RuntimeException('La versión actual de PHP es ' . phpversion() . ' y como mínimo se require la versión 7.0.0');
}

//---------------------------------------------------------------------------------------------

/**
 * Lectura y escritura de caché para Twig
*/
if (!is_writable(API_INTERFACE . 'app/templates/.cache/') || !is_readable(API_INTERFACE . 'app/templates/.cache/')) {
  throw new \RuntimeException('Debe conceder permisos de escritura y lectura a la ruta ' . API_INTERFACE . 'app/templates/.cache/ ó crearla si no existe.');
}

//---------------------------------------------------------------------------------------------

/**
 * Obtiene la configuración inicial del sistema, conexión a la base de datos,
 * constantes de phpmailer, credenciales de la api de paypal, etc.
*/
$config = (new Config)->readConfig();

//---------------------------------------------------------------------------------------------

/**
 * Capa orientada a objetos para el uso de sesiones más seguras en PHP
*/
$session = new Session(new NativeSessionStorage(
  array(
    'cookie_lifetime' => $config['sessions']['life_time']
  )
));
$session->start();

//---------------------------------------------------------------------------------------------

/**
 * Capa orientada a objetos de HTTPFoundation
*/
$http = Request::createFromGlobals();

//---------------------------------------------------------------------------------------------

/**
 * Estatus del DEBUG
*/
if ($config['framework']['debug']) {
  Debug::enable();
}

//---------------------------------------------------------------------------------------------

/**
 * Define el timezone actual
*/
date_default_timezone_set($config['site']['timezone']);