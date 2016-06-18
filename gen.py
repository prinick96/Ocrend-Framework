# -*- coding: utf-8 -*-
import sys
import os

'''
  * OCREND Framework - PHP Code Generator
  * Python 2.7
  * @package OCREND Framework
  * @link http://www.ocrend.com/framework
  * @author Brayan Narváez (Prinick) <prinick@ocrend.com>
  * @copyright 2016 - Ocrend Software
'''

R_MODELS = './core/models/'
R_CONTROLLERS = './core/controllers/'
R_VIEWS = './templates/'

def create_file(route,filename,ext):
    if ext == '.twig' and not os.path.isdir(route + filename + '/'):
        os.mkdir(route + filename + '/')
        e = open(route + filename + '/' + filename + ext,'a')
    else:
        e = open(route + filename + ext,'a')

    if route == R_MODELS:
        e.write('<?php \n\n')
        e.write('final class ' + filename + ' extends Models implements OCREND {\n\n')
        e.write('\tpublic function __construct() {\n')
        e.write('\t\tparent::__construct();\n')
        e.write('\t}\n\n');
        e.write('\tpublic function __destruct() {\n')
        e.write('\t\tparent::__destruct();\n')
        e.write('\t}\n');
        e.write('}\n\n');
        e.write('?>')
    elif route == R_CONTROLLERS:
        e.write('<?php \n\n')
        e.write('class ' + filename + ' extends Controllers {\n\n')
        e.write('\tpublic function __construct() {\n')
        e.write('\t\tparent::__construct();\n')
        e.write('\t\techo $this->template->render(\'' + filename.replace('Controller', '.twig') + '\');\n')
        e.write('\t}\n');
        e.write('}\n\n');
        e.write('?>')
    else:
        e.write('{% include \'overall/header.twig\' %} \n')
        e.write('<body> \n')
        e.write('\t' + filename + '\n')
        e.write('{% include \'overall/footer.twig\' %} \n')
        e.write('</body> \n')
        e.write('</html> \n')

    e.close()
    print "Creado " + route + filename + ext

def check_file(route,filename,ext = '.php'):
    if os.path.exists(route + filename + ext):
        print "ERROR: El archivo " + route + filename + ext + " ya existe"
    else:
        create_file(route,filename,ext)

def main():
    print ""
    arg = sys.argv
    if len(arg) == 3:
        model = arg[2].capitalize()
        controller = arg[2].lower() + 'Controller'
        view = arg[2].lower()

        if arg[1] == 'm':
            check_file(R_MODELS,model)

        elif arg[1] == 'v':
            check_file(R_VIEWS,view,'.twig')

        elif arg[1] == 'c':
            check_file(R_CONTROLLERS,controller)

        elif arg[1] == 'mv':
            check_file(R_MODELS,model)
            check_file(R_VIEWS,view,'.twig')

        elif arg[1] == 'mvc':
            check_file(R_MODELS,model)
            check_file(R_CONTROLLERS,controller)
            check_file(R_VIEWS,view,'.twig')

        elif arg[1] == 'mc':
            check_file(R_MODELS,model)
            check_file(R_CONTROLLERS,controller)

        elif arg[1] == 'cv':
            check_file(R_CONTROLLERS,controller)
            check_file(R_VIEWS,view,'.twig')

        else:
            print u"Módulo no encontrado, escribir en consola: \"python gen.py -ayuda\""
    elif len(arg) == 1:
        print u"Es necesario escribir los módulos a crear"
    else:
        if arg[1] == '-ayuda':
            print '-Crear Modelo: python gen.py m Modulo\n'
            print '-Crear Vista: python gen.py v Modulo\n'
            print '-Crear Controlador: python gen.py c Modulo\n'
            print '-Crear Modelo y Vista: python gen.py mv Modulo\n'
            print '-Crear Modelo, Vista y Controlador: python gen.py mvc Modulo\n'
            print '-Crear Modelo y Controlador: python gen.py mc Modulo\n'
            print '-Crear Controlador y Vista: python gen.py cv Modulo'
        else:
            print u"Es necesario escribir el nombre del módulo a crear"

if __name__ == '__main__':
    main()
