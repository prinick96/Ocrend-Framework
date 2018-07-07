# Ocrend-Framework

<p align="center"><img src="https://framework.ocrend.com/ocrend_framework.png" alt="Ocrend Framework 3" title="Ocrend Framework 3"></p>
<p align="center">
<a href="https://scrutinizer-ci.com/g/prinick96/Ocrend-Framework/" target="_blank"><img src="https://scrutinizer-ci.com/g/prinick96/Ocrend-Framework/badges/quality-score.png?b=master" alt="Scrutinizer 9.41" /></a>
<img src="https://scrutinizer-ci.com/g/prinick96/Ocrend-Framework/badges/build.png?b=master" alt="Build Passed" />
<img src="https://img.shields.io/packagist/l/doctrine/orm.svg" alt="Hecho en PHP 7" />
<img src="https://img.shields.io/badge/php-7-blue.svg" alt="Licencia MIT" />
<img src="https://img.shields.io/badge/stable-3.0.1-blue.svg" alt="Versión Estable" />
</p>

## Introducción
### ¿Qué es Ocrend Framework 3?

Es un framework sencillo y robusto, escrito en **PHP 7** que utiliza la arquitectura **MVC** y componentes de symfony como base de su aplicación en el desarrollo web, adicionalmente pretende acelerar el proceso de desarrollo con unas cuantas herramientas. La curva de aprendizaje es bastante baja, el concepto del framework es ofrecer una arquitectura de sencillo manejo, inclusive para aquellos que jamás han programado utilizando MVC.

### ¿Por qué utilizarlo?

* No requiere manejo de una shell (aunque existe la posibilidad con un pequeño programa escrito en php por consola)
* Es pequeño y de muy fácil aprendizaje
* Es eficiente y seguro
* Fomenta la creación de código limpio, comentado, bien estructurado y eficiente
* Se configura en 1 minuto
* No estás interesado en librerías gigantes como PEAR
* No estás interesado en aprender un framework gigante como Symfony, Laravel o ZendFramework
* No necesitas gestionar rutas y configurar url amigables
* Incluye Silex en sus dependencias, para manejo de API REST correctamente configurado
* Soporte de múltiples bases de datos, utilizando PDO y Drivers nativos para obtener el máximo rendimiento
  * MySQL 5.7+
  * SQLite


## Requisitos

Para colocar el framework se requiere un servidor que cumpla con las siguientes características:

* PHP 7
* APACHE 2
* Cualquier motor de base de datos de los mencionados anteriormente

## Configuración

Abrir el fichero **./Ocrend/Kernel/Config/Ocrend.ini.yml*
```yml
site:
  production: Establecer en true, sólamente cuando esté en el servidor de producción
  name: Nombre de su aplicación web
  url: URL completa para acceder al framework, es importante el "/" del final
  
router:
  ssl: Establecer en true, para especificar si se trabaja con el protocolo HTTPS
  path: Ruta de instalación
   
```
Si modificamos correctamente esos datos, y guardamos el archivo, ya podremos empezar a trabajar.

## Hola Mundo desde consola
Es tan sencillo como hacer lo siguiente en consola desde la ruta principal del framework
```
php gen app:c Hola v
```
Y luego accedemos desde la url a www.miweb.com/hola/

## Documentación

[Web Oficial](https://framework.ocrend.com) -
[Comunidad para Soporte](https://foro.ocrend.com)

## Cómo contribuir

- Realizar un fork
- Crear una rama con el nombre del feature o bugfix
- Realizar el pull request de la rama
- Esperar por el merge
