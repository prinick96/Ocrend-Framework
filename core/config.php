<?php

# Tipado estricto para PHP 7
declare(strict_types=1);

//------------------------------------------------

# Seguridad
defined('INDEX_DIR') OR exit('Ocrend software says .i.');

//------------------------------------------------

# Timezone DOC http://php.net/manual/es/timezones.php
date_default_timezone_set('America/Caracas');

//------------------------------------------------

/**
  * Configuración de la conexión con la base de datos.
  * @param host 'hosting local/remoto'
  * @param user 'usuario de la base de datos'
  * @param pass 'password del usuario de la base de datos'
  * @param name 'nombre de la base de datos'
  * @param port 'puerto de la base de datos (no necesario en la mayoría de motores)'
  * @param protocol 'protocolo de conexión (no necesario en la mayoría de motores)'
  * @param motor 'motor de conexión por defecto'
  * MOTORES VALORES:
  *        mysql
  *        sqlite
  *        oracle
  *        postgresql
  *        cubrid
  *        firebird
  *        odbc
  *        mssql: DOC https://github.com/prinick96/Ocrend-Framework/issues/7
*/
define('DATABASE', array(
  'host' => 'localhost',
  'user' => 'root',
  'pass' => '',
  'name' => 'ocrend',
  'port' => 1521,
  'protocol' => 'TCP',
  'motor' => 'mysql'
));

//------------------------------------------------

/**
  * Define la carpeta en la cual se encuentra instalado el framework.
  * @example "/" si para acceder al framework colocamos http://url.com en la URL, ó http://localhost
  * @example "/Ocrend-Framework/" si para acceder al framework colocamos http://url.com/Ocrend-Framework, ó http://localhost/Ocrend-Framework/
*/
define('__ROOT__', '/Ocrend-Framework/');

//------------------------------------------------

# Constantes fundamentales
define('URL', 'http://localhost/Ocrend-Framework/');
define('APP', 'Ocrend Framework');

//------------------------------------------------

# Control de sesiones
define('DB_SESSION', false);
define('SESSION_TIME', 18000); # Tiempo de vida para las sesiones 5 horas = 18000 segundos.
define('SESS_APP_ID', 'app_id');
session_start([
  'use_strict_mode' => true,
  'use_cookies' => true,
  'cookie_lifetime' => SESSION_TIME,
  'cookie_httponly' => true # Evita el acceso a la cookie mediante lenguajes de script (cómo javascript)
]);

//------------------------------------------------

# Constantes de PHPMailer
define('PHPMAILER_HOST', '');
define('PHPMAILER_USER', '');
define('PHPMAILER_PASS', '');
define('PHPMAILER_PORT', 465);

//------------------------------------------------

# PayPal SDK
define('PAYPAL_MODE','sandbox'); # sandbox ó live
define('PAYPAL_CLIENT_ID','');
define('PAYPAL_CLIENT_SECRET','');

//------------------------------------------------

# Activación del Firewall
define('FIREWALL', true);

//------------------------------------------------

# Activación del DEBUG, solo para desarrollo
define('DEBUG', false);

//------------------------------------------------

# Verifica cual es el motor de templates actual (TWIG:true o PLATESPHP:false)
define('USE_TWIG_TEMPLATE_ENGINE', false);

//------------------------------------------------

# Versión actual del framework
define('VERSION', '1.2');

?>
