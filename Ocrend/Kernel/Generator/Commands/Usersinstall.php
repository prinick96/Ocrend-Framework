<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Ocrend\Kernel\Generator\Commands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Helper\ProgressBar;
use Ocrend\Kernel\Database\Database;
use Ocrend\Kernel\Helpers as Helper;

/**
 * Información inicial de comandos
 * 
 * @author Brayan Narváez <prinick@ocrend.com>
 */
class Usersinstall extends Command {

    protected function configure() {
        $this
        ->setName('users:install')
        ->setDescription('Instala todo el sistema de usuarios')
        ->setHelp('Instala todo el sistema de usuarios, con login, registro, logout y recuperar clave');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        global $config;

        # Salto de linea
        $output->writeln([
            '',
        ]);

        $progressBar = new ProgressBar($output, 5);
        $progressBar->start();

        # Conectar con la base de datos
        $db = Database::resolveDriver($config['database']['default_driver']);

        # Avanzar progreso
        $progressBar->advance();

        # Instalar la base de datos
        $db->query("CREATE TABLE IF NOT EXISTS `users` (
            `id_user` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` varchar(100) NOT NULL,
            `email` varchar(150) UNIQUE NOT NULL,
            `pass` varchar(90) NOT NULL,
            `tmp_pass` varchar(90) NULL DEFAULT NULL,
            `token` varchar(90) NULL DEFAULT NULL,
        PRIMARY KEY (`id_user`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        # Avanzar progreso
        $progressBar->advance();

        # Crear controlador login
        Helper\Files::write_file('./app/controllers/loginController.php', '<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/
         
namespace app\controllers;
        
use app\models as Model;
use Ocrend\Kernel\Helpers as Helper;
use Ocrend\Kernel\Controllers\Controllers;
use Ocrend\Kernel\Controllers\IControllers;
use Ocrend\Kernel\Router\IRouter;
        
/**
 * Controlador login/
*/
class loginController extends Controllers implements IControllers {
        
    public function __construct(IRouter $router) {
        parent::__construct($router,array(
            \'users_not_logged\' => true
        ));
        $this->template->display(\'login/login\');
    }
}');
        # Crear controlador registro
        Helper\Files::write_file('./app/controllers/registerController.php', '<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/
        
namespace app\controllers;
        
use app\models as Model;
use Ocrend\Kernel\Helpers as Helper;
use Ocrend\Kernel\Controllers\Controllers;
use Ocrend\Kernel\Controllers\IControllers;
use Ocrend\Kernel\Router\IRouter;
        
/**
 * Controlador register/
*/
class registerController extends Controllers implements IControllers {
        
    public function __construct(IRouter $router) {
        parent::__construct($router,array(
            \'users_not_logged\' => true
        ));
        $this->template->display(\'register/register\');
    }
}');

        # Crear controlador logout
        Helper\Files::write_file('./app/controllers/logoutController.php', '<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/
        
namespace app\controllers;
        
use app\models as Model;
use Ocrend\Kernel\Helpers as Helper;
use Ocrend\Kernel\Controllers\Controllers;
use Ocrend\Kernel\Controllers\IControllers;
use Ocrend\Kernel\Router\IRouter;
        
/**
 * Controlador logout/
*/
class logoutController extends Controllers implements IControllers {
        
    public function __construct(IRouter $router) {
        parent::__construct($router);
        $u = new Model\Users;
        $u->logout();
    }
}');

        # Crear controlador lostpass
        Helper\Files::write_file('./app/controllers/lostpassController.php', '<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/
        
namespace app\controllers;
        
use app\models as Model;
use Ocrend\Kernel\Helpers as Helper;
use Ocrend\Kernel\Controllers\Controllers;
use Ocrend\Kernel\Controllers\IControllers;
use Ocrend\Kernel\Router\IRouter;
        
/**
 * Controlador lostpass/
*/
class lostpassController extends Controllers implements IControllers {
        
    public function __construct(IRouter $router) {
        parent::__construct($router,array(
            \'users_not_logged\' => true
        ));
        (new Model\Users)->changeTemporalPass();
    }
}');

        # Avanzar progreso
        $progressBar->advance();

        # Crear vista login
        Helper\Files::create_dir('./app/templates/login/');
        Helper\Files::write_file('./app/templates/login/login.twig','{% extends \'overall/layout\' %}
{% block appBody %}
    <form role="form" id="login_form">
        <input type="email" name="email" placeholder="Email" />
        <input type="password" name="pass" placeholder="Contraseña" />
        <button type="button" id="login">Iniciar</button>
    </form>
        
    <form role="form" id="lostpass_form">
        <input type="email" name="email" placeholder="Email" />
        <button type="button" id="lostpass">Recuperar contraseña</button>
    </form>
{% endblock %}
{% block appFooter %}
    <script src="assets/jscontrollers/login/login.js"></script>
    <script src="assets/jscontrollers/lostpass/lostpass.js"></script>
{% endblock %}');

        # Crear vista registro
        Helper\Files::create_dir('./app/templates/register/');
        Helper\Files::write_file('./app/templates/register/register.twig','{% extends \'overall/layout\' %}
{% block appBody %}
    <form role="form" id="register_form">
        <input type="text" name="name" placeholder="Nombre" />
        <input type="email" name="email" placeholder="Email" />
        <input type="password" name="pass" placeholder="Contraseña" />
        <input type="password" name="pass_repeat" placeholder="Repetir Contraseña" />
        <button type="button" id="register">Registrar</button>
    </form>
{% endblock %}
{% block appFooter %}
    <script src="assets/jscontrollers/register/register.js"></script>
{% endblock %}');

        # Avanzar progreso
        $progressBar->advance();

        # Crear js login
        Helper\Files::create_dir('./assets/jscontrollers/login/');
        Helper\Files::write_file('./assets/jscontrollers/login/login.js','/**
 * Ajax action to api rest
*/
function login(){
    var $ocrendForm = $(this), __data = {};
    $(\'#login_form\').serializeArray().map(function(x){__data[x.name] = x.value;}); 
       
    if(undefined == $ocrendForm.data(\'locked\') || false == $ocrendForm.data(\'locked\')) {
        $.ajax({
            type : "POST",
            url : "api/login",
            dataType: \'json\',
            data : __data,
            beforeSend: function(){ 
                $ocrendForm.data(\'locked\', true) 
            },
            success : function(json) {
                if(json.success == 1) {
                    alert(json.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    alert(json.message);
                }
            },
            error : function(xhr, status) {
                alert(\'Ha ocurrido un problema interno\');
            },
            complete: function(){ 
                $ocrendForm.data(\'locked\', false);
            } 
        });
    }
}

/**
 * Events
 */
$(\'#login\').click(function(e) {
    e.defaultPrevented;
    login();
});
$(\'form#login_form input\').keypress(function(e) {
    e.defaultPrevented;
    if(e.which == 13) {
        login();

        return false;
    }
});');

        # Crear js register
        Helper\Files::create_dir('./assets/jscontrollers/register/');
        Helper\Files::write_file('./assets/jscontrollers/register/register.js','/**
 * Ajax action to api rest
*/
function register(){
    var $ocrendForm = $(this), __data = {};
    $(\'#register_form\').serializeArray().map(function(x){__data[x.name] = x.value;}); 

    if(undefined == $ocrendForm.data(\'locked\') || false == $ocrendForm.data(\'locked\')) {
        $.ajax({
            type : "POST",
            url : "api/register",
            dataType: \'json\',
            data : __data,
            beforeSend: function(){ 
                $ocrendForm.data(\'locked\', true) 
            },
            success : function(json) {
                if(json.success == 1) {
                    alert(json.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    alert(json.message);
                }
            },
            error : function(xhr, status) {
                alert(\'Ha ocurrido un problema interno\');
            },
            complete: function(){ 
                $ocrendForm.data(\'locked\', false);
            } 
        });
    }
} 

/**
 * Events
 */
$(\'#register\').click(function(e) {
    e.defaultPrevented;
    register();
});
$(\'form#register_form input\').keypress(function(e) {
    e.defaultPrevented;
    if(e.which == 13) {
        register();

        return false;
    }
});');

        # Crear js lostpass
        Helper\Files::create_dir('./assets/jscontrollers/lostpass/');
        Helper\Files::write_file('./assets/jscontrollers/lostpass/lostpass.js','/**
 * Ajax action to api rest
*/
function lostpass(){
    var $ocrendForm = $(this), __data = {};
    $(\'#lostpass_form\').serializeArray().map(function(x){__data[x.name] = x.value;}); 

    if(undefined == $ocrendForm.data(\'locked\') || false == $ocrendForm.data(\'locked\')) {
        $.ajax({
            type : "POST",
            url : "api/lostpass",
            dataType: \'json\',
            data : __data,
            beforeSend: function(){ 
                $ocrendForm.data(\'locked\', true) 
            },
            success : function(json) {
                if(json.success == 1) {
                    alert(json.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    alert(json.message);
                }
            },
            error : function(xhr, status) {
                alert(\'Ha ocurrido un problema interno\');
            },
            complete: function(){ 
                $ocrendForm.data(\'locked\', false);
            } 
        });
    }
} 

/**
 * Events
 */
$(\'#lostpass\').click(function(e) {
    e.defaultPrevented;
    lostpass();
});
$(\'form#lostpass_form input\').keypress(function(e) {
    e.defaultPrevented;
    if(e.which == 13) {
        lostpass();

        return false;
    }
});');
        
        # 100% de progreso
        $progressBar->finish();

        # Exito
        $output->writeln([
            '',
            '',
            ' Sistema de usuarios agregado'
        ]);
    }
}