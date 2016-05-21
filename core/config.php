<?php

#tipado estricto para PHP 7
declare(strict_types=1);

#control global de sesiones
session_start();

#configuración de conxión con base de datos
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASS','');
define('DB_NAME','ocrend');

#constantes elementales
define('URL','http://prinick-notebook/Ocrend-Framework/');
define('APP','Ocrend-Framework');

?>
