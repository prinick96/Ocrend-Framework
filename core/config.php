<?php

#tipado estricto para PHP 7
declare(strict_types=1);

#control global de sesiones
session_start();

#idioma base
setlocale(LC_ALL,"es_ES"); //comentar si servidor no soporta setlocale

#configuración de conxión con base de datos
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASS','');
define('DB_NAME','ocrend');

#constantes elementales
define('URL','http://prinick-notebook/Ocrend-Framework/');
define('APP','Ocrend-Framework');

#Constantes de PHPMailer
define('PHPMAILER_HOST','p3plcpnl0173.prod.phx3.secureserver.net');
define('PHPMAILER_USER','ocrend@ocrend.com');
define('PHPMAILER_PASS','CaX5487B!89');
define('PHPMAILER_PORT',465);

?>
