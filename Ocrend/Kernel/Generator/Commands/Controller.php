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
class Controller extends Command {

    protected function configure() {
        $this
        ->setName('app:c')
        ->setDescription('Crea un nuevo controlador')
        ->setHelp('Este comando se ocupa para crear un controlador nuevo')
        ->addArgument('controllername', InputArgument::REQUIRED, 'El nombre del controlador')
        ->addArgument('extra', InputArgument::OPTIONAL, 'Otras entidades a crear')
        ->addOption('db', null, InputOption::VALUE_OPTIONAL, 'Si se especifica un modelo, saber si se conectara con la base de datos', 1)
        ->addOption('ajax', null, InputOption::VALUE_OPTIONAL, 'Si se especifica un modelo, define si se quiere establecer una conexión con la api', 1)
        ->addOption('nocreatemodel', null, InputOption::VALUE_OPTIONAL, 'Ignora la creacion de una modelo', false)
        ->addOption('nocreateview', null, InputOption::VALUE_OPTIONAL, 'Ignora la creacion de una modelo', false)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        # Nombre del controlador
        $controllerName = strtolower($input->getArgument('controllername'));

        # Ruta del controlador
        $routeController = './app/controllers/' . $controllerName . 'Controller.php';

        # Verificar si ya existe
        if(file_exists($routeController)) {
            $io = new SymfonyStyle($input, $output);
            $choice = $io->ask('ADVERTENCIA: El controlador ' . $controllerName . ' ya existe, desdea sobreescribirlo? [si][no]');
            
            if($choice != 'si') {
                exit('Ha salido del generador.');
            }
        }

        # Obtener contenido
        $controllerContent = Helper\Files::read_file('./Ocrend/Kernel/Generator/Content/controller');

        # Reemplazar lo elemental
        $controllerContent = str_replace('{{controller}}', $controllerName, $controllerContent);

        # Verificar opciones
        $options = strlen($input->getArgument('extra'));

        # Reemplazo por defecto
        $extra_functions = '';

        # Analizar las opciones
        if($options > 0) {
            $database = false;
            $ajax = false;
            $model = false;

            # Crear un modelo
            if(strpos($input->getArgument('extra'), 'm') !== false) {
                $model = true;
                
                if(1 != $input->getOption('db')) {
                    $database = true;
                }
                
                if(1 != $input->getOption('ajax')) {
                    $ajax = true;
                }

                # Nombre del modelo
                $modelName = ucfirst($controllerName);

                if(false == $input->getOption('nocreatemodel')) {
                    $create_model = $this->getApplication()->find('app:m');
                    $arguments = array(
                        'command' => 'app:m',
                        'modelname' => $modelName,
                        '--nocreateview' => true,
                        '--nocreatecontroller' => true
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
                
                $model_var = $controllerName[0];    
                $extra_functions = '$'.$model_var.' = new Model\\' . $modelName . ";\n\t\t";
            }

            # Crear una vista
            if(strpos($input->getArgument('extra'), 'v') !== false) {
                $extra_functions .= '$this->template->display(\''. $controllerName .'/'. $controllerName .'\');';
                if(false == $input->getOption('nocreateview')) {
                    $create_controller = $this->getApplication()->find('app:v');
                    $arguments = array(
                        'command' => 'app:v',
                        'viewname' => $controllerName,
                        'extra'  => 'm',
                        '--nocreatemodel' => true,
                        '--nocreatecontroller' => true
                    );
                    if($ajax) {
                        $arguments['--ajax'] = 0;
                    }
                    $greetInput = new ArrayInput($arguments);
                    $returnCode = $create_controller->run($greetInput, $output);
                }
            }
            
        }

        # Realizar reemplazos
        $controllerContent = str_replace('{{extra_functions}}', $extra_functions, $controllerContent);

        # Crear controlador
        Helper\Files::write_file($routeController, $controllerContent);

        $output->writeln([
            '',
            'Controlador ' . $input->getArgument('controllername') . ' creado '
        ]);
    }
}