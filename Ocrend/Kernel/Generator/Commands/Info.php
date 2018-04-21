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

/**
 * Información inicial de comandos
 * 
 * @author Brayan Narváez <prinick@ocrend.com>
 */
class Info extends Command {

    protected function configure() {
        $this
        ->setName('info')
        ->setDescription('Da información acerca del generador')
        ->setHelp('Este comando se ocupa de generar información acerca de la consola');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        global $config;

        $output->writeln([
            '',
            'COMANDOS - ' . $config['build']['name']
        ]);
    }
}