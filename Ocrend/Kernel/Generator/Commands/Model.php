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
 * Comando para crear modelos
 * 
 * @author Brayan Narváez <prinick@ocrend.com>
 */
class Model extends Command {

    protected function configure() {
        $this
        ->setName('app:m')
        ->setDescription('Crea un nuevo modelo')
        ->setHelp('Este comando se ocupa para generar un modelo nuevo')
        ->addArgument('modelname', InputArgument::REQUIRED, 'El nombre del controlador')
        ->addArgument('extra', InputArgument::OPTIONAL, 'Otras entidades a crear')
        ->addOption('db', null, InputOption::VALUE_OPTIONAL, 'Saber si se conectara con la base de datos', 1)
        ->addOption('ajax', null, InputOption::VALUE_OPTIONAL, 'Define si se quiere establecer una conexión con la api', 1)
        ->addOption('nocreateview', null, InputOption::VALUE_OPTIONAL, 'Ignora la creacion de una vista', false)
        ->addOption('nocreatecontroller', null, InputOption::VALUE_OPTIONAL, 'Ignora la creacion de un controlador', false)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        # Nombre del modelo
        $modelName = ucfirst($input->getArgument('modelname'));

        # Ruta del modelo
        $routeModel = './app/models/' . $modelName . '.php';

        # Verificar si ya existe
        if(file_exists($routeModel)) {
            $io = new SymfonyStyle($input, $output);
            $choice = $io->ask('ADVERTENCIA: El modelo ' . $modelName . ' ya existe, desdea sobreescribirlo? [si][no]');
            
            if($choice != 'si') {
                exit('Ha salido del generador.');
            }
        }

        # Obtener contenido
        $modelContent = Helper\Files::read_file('./Ocrend/Kernel/Generator/Content/model');

        # Reemplazar lo elemental
        $modelContent = str_replace('{{model}}', $modelName, $modelContent);

        # Verificar opciones
        $options = strlen($input->getArgument('extra'));

        # Crea el javascript y el endpoint de la api
        $content = '';
        $ajax = false;
        if(1 != $input->getOption('ajax')) {
            $ajax = true;
            $content = "
    /**
     * Respuesta generada por defecto para el endpoint
     * 
     * @return array
    */ 
    public function foo() : array {
        try {
            global \$http;
                    
            return array('success' => 0, 'message' => 'Funcionando');
        } catch(ModelsException \$e) {
            return array('success' => 0, 'message' => \$e->getMessage());
        }
    }\n";
        }
        $modelContent = str_replace('{{content}}', $content, $modelContent);

        # Crea el modelo con la base de datos
        $trait_db_model = '';
        $trait_db_model_construct = '';
        if(1 != $input->getOption('db')) {
            $trait_db_model = 'use DBModel;';
            $trait_db_model_construct = "\n\t\t\$this->startDBConexion();";
        }        
        $modelContent = str_replace('{{trait_db_model}}', $trait_db_model, $modelContent);
        $modelContent = str_replace('{{trait_db_model_construct}}', $trait_db_model_construct, $modelContent);

        # Analizar las opciones
        if($options > 0) {
            # Nombre de controlador (válido para vista)
            $controllerName = strtolower($input->getArgument('modelname'));

            # Crear una vista
            $view = false;
            if(strpos($input->getArgument('extra'), 'v') !== false) {
                $view = true;

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

            # Crear un controlador
            if(strpos($input->getArgument('extra'), 'c') !== false && false == $input->getOption('nocreatecontroller')) {
                $create_controller = $this->getApplication()->find('app:c');
                $arguments = array(
                    'command' => 'app:c',
                    'controllername' => $controllerName,
                    'extra'  => 'm' . ($view ? 'v' : ''),
                    '--nocreatemodel' => true,
                    '--nocreateview' => true
                );
                $greetInput = new ArrayInput($arguments);
                $returnCode = $create_controller->run($greetInput, $output);
            }
        }

        # Crear modelo
        Helper\Files::write_file($routeModel, $modelContent);

        $output->writeln([
            '',
            'Modelo ' . $input->getArgument('modelname') . ' creado '
        ]);
    }
}