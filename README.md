# Ocrend-Framework

>**PHP 7**

>**@link** http://www.ocrend.com/

>**@author** Brayan Narváez (Prinick) - <prinick@ocrend.com>

>**@copyright** 2016 Ocrend Software

>**@license** Private code

## Instalación
### Descarga
```
  git clone https://github.com/prinick96/Ocrend-Framework.git
```
### Configuración

En caso de estar en LINUX y obtener problemas de persmisos de escritura con Twig o el Firewall, poner en la consola lo siguiente:
```
  ~$ sudo chmod -R 777 /ruta/en/donde/esta/el/framework
```

__./core/config.php__
```php

  setlocale(LC_ALL,"es_ES"); #En caso de que el servidor de un warning, comentar esta línea, significa que no soporta setlocale

  define('DATABASE', array(
    'host' => 'localhost', #Servidor para conexión con la base de datos
    'user' => 'root', #Usuario para la base de datos
    'pass' => '', #Contraseña del usuario para la base de datos
    'name' => 'ocrend', #Nombre de la base de datos
    'motor' => 'mysql' #Motor de la base de datos
  ));

  define('URL','http://prinick-notebook/Ocrend-Framework/'); #Url en donde está instalado el framework, importante el "/" al final
  define('APP','Ocrend-Framework'); #Nombre de la aplicación, este también sale en <title></title>, correos, footer y demás

  #Por defecto esta es la conexión SMTP para enviar correos en los entornos de desarrollo, luego se ha de cambiar en producción.
  define('PHPMAILER_HOST','p3plcpnl0173.prod.phx3.secureserver.net');
  define('PHPMAILER_USER','ocrend@ocrend.com');
  define('PHPMAILER_PASS','CaX5487B!89');
  define('PHPMAILER_PORT',465);

  /**
    * Define la carpeta en la cual se encuentra instalado el framework.
    * @example "/" si para acceder al framework colocamos http://url.com en la URL, ó http://localhost
    * @example "/Ocrend-Framework/" si para acceder al framework colocamos http://url.com/Ocrend-Framework, ó http://localhost/Ocrend-Framework/
  */
  define('__ROOT__','/Ocrend-Framework/');

  define('FIREWALL',true); #Activación del firewall que ofrece protección contra múltiples ataques comunes

  define('DEBUG',true); #Establecer en FALSE una vez esté todo el producción, en desarrollo mantener en true
```
__./core/models/Func.php__
```php
  #En caso de obtener error de send_mail con PHPMailer, comentar esta línea:
  $mail->isSendMail();
  #Descomentar:
  $mail->isSMTP(); //Es más lento, pero es seguro.
```
__./core/kernel/Firewall.php__
```php
  #Línea 6
    'WEBMASTER_EMAIL' => 'prinick@ocrend.com', //En caso de ataque, se enviará un email a este correo notificando
  #Línea 7
    'PUSH_MAIL' => false, //En caso de ataque, aquí se activa el envío de un email de alerta al correo en WEBMASTER_EMAIL
```

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
Crear __./templates/hola/hola.phtml__
```phtml
<?= $this->insert('overall/header') ?>
<body id="page-top" class="index">

  <?= $this->insert('overall/topnav') ?>

    <section id="about">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 text-center">
                    <h2>¡Creando un Hola Mundo!</h2>
                </div>
                <div class="col-sm-12 text-center">
                  <br />
                  <p>¡Hola mundo!</p>
                </div>
            </div>
        </div>
    </section>

<?= $this->insert('overall/footer') ?>
</body>
</html>
```
Acceder a http://url.com/hola/

## Generador de código PHP

__Requiere Python 2.* instalado para funcionar__

El generador de código PHP, es muy sencillo y está escrito en Python, se encuentra en __./gen.py__ y es de libre edición como todo el framework, la idea de este generador es tener una pequeña herramienta para agilizar el proceso de escribir muchas veces el mismo molde al momento de crear Modelos, Vistas o Controladores para empezar a programar.

Ir a la consola, sea en Windows, Linux o Mac y escribir:
```
  ~$ cd /ruta/en/donde/esta/el/framework/
```
A continuación escribir el comando para generar un módulo completo (Modelo,Vista y Controlador):
```
  ~$ python gen.py mvc Ejemplo
```
Debería de aparecer en consola, tres mensajes que indican la creación de tres archivos, entonces ya podríamos entrar a http://url.com/ejemplo/

Para __más información acerca de los comandos__ escribir:
```
  ~$ python gen.py -ayuda
```
