# Ocrend-Framework

![Hecho en PHP 7](https://img.shields.io/packagist/l/doctrine/orm.svg)
![Licencia MIT](https://img.shields.io/badge/php-7-blue.svg)
![Versión Estable](https://img.shields.io/badge/stable-1.2.4-blue.svg)

## Introducción
### ¿Qué es Ocrend Framework?

Es un framework sencillo escrito en **PHP 7** que utiliza la arquitectura **MVC** como base de su aplicación en el desarrollo web, adicionalmente pretende acelerar el proceso de desarrollo con unas cuantas herramientas. La curva de aprendizaje es bastante baja, el concepto del framework es ofrecer una arquitectura de sencillo manejo, inclusive para aquellos que jamás han programado utilizando MVC.

### ¿Por qué utilizarlo?

* No requiere manejo de una shell (aunque existe la posibilidad con un pequeño programa escrito en php por consola)
* Es pequeño y de muy fácil aprendizaje
* Es eficiente y seguro
* Fomenta la creación de código limpio, comentado, bien estructurado y eficiente
* Se configura en 2 minutos y se puede empezar a desarrollar con el
* No estás interesado en librerías gigantes como PEAR
* No estás interesado en aprender un framework gigante como Symfony, Laravel o ZendFramework
* No necesitas gestionar rutas usando namespaces o requires/includes, el framework lo hace por tí
* Incluye Slim framework 3 en sus dependencias, para manejo de API REST correctamente configurado
* Soporte de múltiples bases de datos con distintos motores usando PDO **simultáneamente**
  * MySQL 5.1+
  * Oracle
  * PostgreSQL
  * MS SQL
  * SQLite
  * CUBRID
  * Interbase/Firebid
  * ODBC

## Requisitos

Para colocar el framework en producción se requiere un VPS, Dedicado o Hosting que cumpla las siguientes características:

* PHP 7
* APACHE 2

## Instalación y Configuración
### Descarga
Clonando el repositorio.
```
  git clone https://github.com/prinick96/Ocrend-Framework.git
```
Descargando el paquete manualmente.

[Ver Descargas](https://github.com/prinick96/Ocrend-Framework/releases)
### Configuración

En caso de estar en LINUX y obtener problemas de persmisos de escritura por el Firewall, poner en la consola lo siguiente:
```
  ~$ sudo chmod -R 777 /ruta/en/donde/esta/el/framework
```

__./core/config.php__
```php

  #En caso de que el servidor de un warning, comentar esta línea, significa que no soporta setlocale
  setlocale(LC_ALL,"es_ES");

  define('DATABASE', array(
    'host' => 'localhost', #Servidor para conexión con la base de datos
    'user' => 'root', #Usuario para la base de datos
    'pass' => '', #Contraseña del usuario para la base de datos
    'name' => 'ocrend', #Nombre de la base de datos
    'port' => 1521, #Puerto de conexión para algunos motores
    'protocol' => 'TCP', #Protocolo de conexión para Oracle
    'motor' => 'mysql' #Motor de la base de datos
  ));

   #Url en donde está instalado el framework, importante el "/" al final
  define('URL', 'http://prinick-notebook/Ocrend-Framework/');

  #Nombre de la aplicación, este también sale en <title></title>, correos, footer y demás
  define('APP', 'Ocrend Framework');

  #Configuración para salida de correos con PHPMailer, sin estos obtendremos un 'SMTP connect() failed'
  define('PHPMAILER_HOST', '');
  define('PHPMAILER_USER', '');
  define('PHPMAILER_PASS', '');
  define('PHPMAILER_PORT', 465);

  /**
    * Define la carpeta en la cual se encuentra instalado el framework.
    * @example "/" si para acceder al framework colocamos http://url.com en la URL, ó http://localhost
    * @example "/Ocrend-Framework/" si para acceder al framework colocamos http://url.com/Ocrend-Framework, ó http://localhost/Ocrend-Framework/
  */
  define('__ROOT__', '/Ocrend-Framework/');

 # Control de sesiones
 define('DB_SESSION', true); # Uso de sesiones con la base de datos
 define('SESSION_TIME', 18000); # Tiempo de vida para las sesiones 5 horas = 18000 segundos.
 define('SESS_APP_ID', 'app_id'); # Nombre de la variable de sesión que contendrá el ID del usuario activo
 session_start([
   'use_strict_mode' => true,
   'use_cookies' => true,
   'cookie_lifetime' => SESSION_TIME,
   'cookie_httponly' => true, # Evita el acceso a la cookie mediante lenguajes de script (cómo javascript)
   'hash_function' => 5 # sha256, para obtener una lista completa print_r(hash_algos());
 ]);

  #Activación del firewall que ofrece protección contra múltiples ataques comunes
  define('FIREWALL', true);

  #Establecer en FALSE una vez esté todo el producción, en desarrollo es recomendando mantener en TRUE
  define('DEBUG', true);
```

__./core/kernel/Firewall.php__
```php
  #Línea 14
    'WEBMASTER_EMAIL' => 'prinick@ocrend.com', //En caso de ataque, se enviará un email a este correo notificando
  #Línea 15
    'PUSH_MAIL' => false, //En caso de ataque, aquí se activa el envío de un email de alerta al correo en WEBMASTER_EMAIL
```

Adicionalmente **ocrend.sql** contiene una tabla llamada users, la cual contiene un usuario de ejemplo, debemos subirla a nuestra base de datos para poder utilizar el login/lostpass/registro que viene previamente programado.


**usuario:** test

**password:** 123456


## Primer Hola Mundo

Crear __./core/controllers/holaController.php__
```php
  class holaController extends Controllers {

    public function __construct() {
      parent::__construct();
      echo $this->template->render('hola/hola');
    }

  }
```
Crear __./templates/plates/hola/hola.phtml__
```phtml
<?= $this->insert('overall/header') ?>
  <body>
    <div class="container">
      <div class="presentacion center">
        <div class="row">
          <div class="col-xs-12">
            <h1>HOLA MUNDOOOO!</h1>
          </div>
        </div>
      </div>
    </div>
    <?= $this->insert('overall/footer') ?>
  </body>
</html>
```
Acceder a http://url.com/hola/

## Generador de código PHP

El generador de código PHP, es muy sencillo y está escrito en PHP, se encuentra en __./gen.php__ y es de libre edición como todo el framework, la idea de este generador es tener una pequeña herramienta para agilizar el proceso de escribir muchas veces el mismo molde al momento de crear Modelos, Vistas o Controladores, tablas en la base de datos o cruds enteros para empezar a programar.

Ir a la consola, sea en Windows, Linux o Mac y escribir:
```
  ~$ cd /ruta/en/donde/esta/el/framework/
```
A continuación escribir el comando para generar un módulo completo (Modelo,Vista y Controlador):
```
  ~$ php gen.php mvc Ejemplo
```
Debería de aparecer en consola, tres mensajes que indican la creación de tres archivos, entonces ya podríamos entrar a http://url.com/ejemplo/

Para __más información acerca de los comandos__ escribir:
```
  ~$ php gen.php -ayuda
```

## Documentación

[Github Wiki](https://github.com/prinick96/Ocrend-Framework/wiki) -
[Web Oficial](http://framework.ocrend.com)
