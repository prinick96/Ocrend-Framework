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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArrayInput;
use Ocrend\Kernel\Helpers as Helper;

/**
 * Comando para crear controladores
 * 
 * @author Brayan Narváez <prinick@ocrend.com>
 */
class View extends Command {

    protected function configure() {
        $this
        ->setName('app:v')
        ->setDescription('Crea una nueva vista')
        ->setHelp('Este comando se ocupa para crear una vista nueva')
        ->addArgument('viewname', InputArgument::REQUIRED, 'El nombre de la vista')
        ->addArgument('extra', InputArgument::OPTIONAL, 'Otras entidades a crear')
        ->addOption('db', null, InputOption::VALUE_OPTIONAL, 'Si se especifica un modelo, saber si se conectara con la base de datos', 1)
        ->addOption('ajax', null, InputOption::VALUE_OPTIONAL, 'Si se especifica un modelo, define si se quiere establecer una conexión con la api', 1)
        ->addOption('nocreatemodel', null, InputOption::VALUE_OPTIONAL, 'Ignora la creacion de una modelo', false)
        ->addOption('nocreatecontroller', null, InputOption::VALUE_OPTIONAL, 'Ignora la creacion de un controlador', false)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        # Nombre de la vista
        $viewname = strtolower($input->getArgument('viewname'));

        # Nombre para el posible javascript de ajax
        $viewAjaxFolder = './assets/jscontrollers/'.$viewname .'/';
        $viewAjax = $viewAjaxFolder . $viewname .'.js';
        if(file_exists($viewAjax)) {
            $viewAjax = $viewAjaxFolder . $viewname . '_' . time() .'.js';
        }

        # Ruta de la vista
        $routeViewFolder = './app/templates/' . $viewname . '/';

        # Verificar si ya existe
        if(file_exists($routeViewFolder . $viewname . '.twig')) {
            $io = new SymfonyStyle($input, $output);
            $choice = $io->ask('ADVERTENCIA: La vista ' . $viewname . '.twig ya existe, desdea sobreescribirla? [si][no]');
            
            if($choice != 'si') {
                exit('Ha salido del generador.');
            }
        }

        # Verificar opciones
        $options = strlen($input->getArgument('extra'));

        # Analizar las opciones
        $ajax = false;
        $ajax_content = '';
        if($options > 0) {
            $model = false;
            $database = false;

            # Crear un modelo
            if(strpos($input->getArgument('extra'), 'm') !== false) {
                $model = true;
                # Nombre del modelo
                $modelName = ucfirst($viewname);

                if(1 != $input->getOption('db')) {
                    $database = true;
                }

                $script = '';
                if(1 != $input->getOption('ajax')) {
                    $ajax = true;
                    $script = '<script src="'.$viewAjax.'"></script>';
                }
                $ajax_content = $script;
                
                if(false == $input->getOption('nocreatemodel')) {
                    $create_model = $this->getApplication()->find('app:m');
                    $arguments = array(
                        'command' => 'app:m',
                        'modelname' => $modelName,
                        '--nocreatecontroller' => true,
                        '--nocreateview' => true
                    );
                    if($database) {
                        $arguments['--db'] = 0;
                    }
                    if($ajax) {
                        $arguments['--ajax'] = 0;
                    }
                    $greetInput = new ArrayInput($arguments);
                    $returnCode = $create_model->run($greetInput, $output);
                }
            }

            # Crear un controlador
            if(strpos($input->getArgument('extra'), 'c') !== false && false == $input->getOption('nocreatecontroller')) {
                $create_controller = $this->getApplication()->find('app:c');
                $arguments = array(
                    'command' => 'app:c',
                    'controllername' => $viewname,
                    'extra' => 'v' . ($model ? 'm' : ''),
                    '--nocreatemodel' => true,
                    '--nocreateview' => true
                );
                if($database) {
                    $arguments['--db'] = 0;
                }
                if($ajax) {
                    $arguments['--ajax'] = 0;
                }
                $greetInput = new ArrayInput($arguments);
                $returnCode = $create_controller->run($greetInput, $output);
            }
            
        }

        # Crear la vista, javasript y escribir en la api restfull
        if($ajax) {
            # Obtener contenido
            $viewContent = Helper\Files::read_file('./Ocrend/Kernel/Generator/Content/viewform');
            $viewContent = str_replace('{{view}}',$viewname,$viewContent);

            # Crear el javascript
            $viewAjaxContent = Helper\Files::read_file('./Ocrend/Kernel/Generator/Content/ajax');
            $viewAjaxContent = str_replace('{{view}}',$viewname,$viewAjaxContent);
            Helper\Files::create_dir($viewAjaxFolder);
            Helper\Files::write_file($viewAjax, $viewAjaxContent);
            $output->writeln([
                '',
                'Fichero javascript ' . $viewAjax . ' creado'
            ]);

            # Escribir en la api
            $viewApiContent = Helper\Files::read_file('./Ocrend/Kernel/Generator/Content/api');
            $viewApiContent = str_replace('{{view}}',$viewname,$viewApiContent);
            $viewApiContent = str_replace('{{model}}',ucfirst($viewname),$viewApiContent);
            $viewApiContent = str_replace('{{model_var}}',$viewname[0],$viewApiContent);
            Helper\Files::write_in_file('./api/controllers/post.controllers.php', $viewApiContent);
            $output->writeln([
                '',
                'Fichero ./api/controllers/post.controllers.php modificado'
            ]);
        } else {
            # Obtener contenido
            $viewContent = Helper\Files::read_file('./Ocrend/Kernel/Generator/Content/view');
        }

        # Crear vista
        $viewContent = str_replace('{{ajax_content}}',$ajax_content,$viewContent);
        Helper\Files::create_dir($routeViewFolder);
        Helper\Files::write_file($routeViewFolder . $viewname . '.twig', $viewContent);
        $output->writeln([
            '',
            'Vista ' . $input->getArgument('viewname') . '.twig creada '
        ]);
    }
}