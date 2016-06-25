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

def create_file(route,filename,ext,api):
    if ext == '.phtml':
        if not os.path.isdir(route + filename + '/'):
            os.mkdir(route + filename + '/')

        e = open(route + filename + '/' + filename + ext,'a')
    else:
        e = open(route + filename + ext,'a')

    if route == R_MODELS:
        e.write('<?php \n\n')
        e.write('final class ' + filename + ' extends Models implements OCREND {\n\n')
        e.write('\tpublic function __construct() {\n')
        e.write('\t\tparent::__construct();\n')
        e.write('\t}\n\n')
        if api:
            e.write('\tfinal public function Foo(array $data) : array {\n')
            e.write('\t\t#...\n')
            e.write('\t\treturn array(\'success\' => 0, \'message\' => \'funcionando\');\n')
            e.write('\t}\n\n');
        e.write('\tpublic function __destruct() {\n')
        e.write('\t\tparent::__destruct();\n')
        e.write('\t}\n')
        e.write('}\n\n')
        e.write('?>')
    elif route == R_CONTROLLERS:
        e.write('<?php \n\n')
        e.write('class ' + filename + ' extends Controllers {\n\n')
        e.write('\tpublic function __construct() {\n')
        e.write('\t\tparent::__construct();\n')
        e.write('\t\techo $this->template->render(\'' + filename.replace('Controller', '/') + filename.replace('Controller', '') + '\');\n')
        e.write('\t}\n')
        e.write('}\n\n')
        e.write('?>')
    else:
        e.write('<?= $this->insert(\'overall/header\') ?> \n')
        e.write('<body class="framework"> \n')
        e.write('\n\n\t<div class="logo">\n\t\t<h3><?= strtoupper(APP) ?></h3>\n\t</div>')
        e.write('\n\n\t<div class="content">')
        e.write('\n\t\t<div class="ocrend-welcome">\n\t\t\t<span class="ocrend-welcome">' + filename.lower() + '</span>\n\t\t\t<span class="ocrend-welcome-subtitle">Vista.</span>\n\t\t</div>')
        e.write('\n\t\t<div class="form-actions">')
        if api:
            e.write('\n\t\t\t<form id="' + filename.lower() + '_form" role="form">\n');
            e.write('\n\t\t\t<div class="alert hide" id="ajax_' + filename.lower() + '"></div>\n\n')
            e.write('\n\t\t\t<div class="form-group">')
            e.write('\n\t\t\t\t<label class="cole">Ejemplo:</label>')
            e.write('\n\t\t\t\t<input type="text" class="form-control form-input" name="ejemplo" placeholder="Escribe algo..." />')
            e.write('\n\t\t\t</div>')
            e.write('\n\t\t\t<div class="form-group">')
            e.write('\n\t\t\t\t<button type="button" id="' + filename.lower() + '" class="btn red  btn-block">Enviar</button>')
            e.write('\n\t\t\t</div>')
            e.write('\n\t\t\t</form>')
            e.write('\n\t\t</div>\n')
            e.write('\n\n\t\t<?= $this->insert(\'overall/footer\') ?> \n')
            e.write('\t<script src="views/app/js/' + filename.lower() + '.js"></script>\n\n\t</div>\n')
        else:
            e.write('\n\t\t\t<p>Vista ' + filename.lower() + '</p>')
            e.write('\n\t\t</div>');
            e.write('\n\n\t\t<?= $this->insert(\'overall/footer\') ?>\n\t</div>\n')
        e.write('</body>\n</html>');

    e.close()
    print "Creado " + route + filename + ext

def check_file(route,filename,ext = '.php',api = False):
    if os.path.exists(route + filename + ext) or os.path.exists(route + filename + '/' + filename + ext):
        print "ERROR: El archivo " + filename + ext + " ya existe"
    else:
        create_file(route,filename,ext,api)

def write_api(method,name):

    e = open('./api/http/' + method + '.php','a+')
    e.write('\n/**\n')
    e.write('\t* ¿qué hace (el modelo que se invoca desde aqui)?\n')
    e.write('\t* @return ¿qué retorna?, ¡un json por favor! \n')
    e.write('*/\n')

    if method == 'get':
        m = '$app->get(\'/'
    else:
        m = '$app->post(\'/'

    e.write(m + name.lower() + '\',function($request, $response) {\n\n')
    e.write('\t$model = new '  + name.capitalize() + ';\n')
    e.write('\t$response->withJson($model->Foo($_' + method.upper() + '));\n\n')
    e.write('\treturn $response;')
    e.write('\n});')
    e.close()

    c = open('./views/app/js/' + name.lower() + '.js','a')
    c.write('/* #'+ name.lower() +' es el ID del botón que acciona este código. */\n')
    c.write('/* #ajax_'+ name.lower() +' es el ID del DIV que muestra resultados y proceso de carga. */\n')
    c.write('/* #'+ name.lower() +'_form es el ID del formulario del cual se recogen todos los datos. */\n\n')
    c.write('$(\'#'+ name.lower() +'\').click(function(){\n\n')
    c.write('\tvar error_icon = \'<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> \',\n')
    c.write('\t\tsuccess_icon = \'<span class="glyphicon glyphicon-ok" aria-hidden="true"></span> \', \n')
    c.write('\t\tprocess_icon = \'<span class="fa fa-spinner fa-spin" aria-hidden="true"></span> \';\n\n')
    c.write('\t$(\'#ajax_'+ name.lower() +'\').removeClass(\'alert-danger\');\n')
    c.write('\t$(\'#ajax_'+ name.lower() +'\').removeClass(\'alert-warning\');\n')
    c.write('\t$(\'#ajax_'+ name.lower() +'\').addClass(\'alert-warning\');\n')
    c.write('\t$(\'#ajax_'+ name.lower() +'\').html(process_icon  + \'Procesando...\');\n')
    c.write('\t$(\'#ajax_'+ name.lower() +'\').removeClass(\'hide\');\n\n')
    c.write('\t$.ajax({\n')
    c.write('\t\ttype : "' + method.upper() + '",\n')
    c.write('\t\turl : "api/'+ name.lower() +'",\n')
    c.write('\t\tdata : $(\'#'+ name.lower() +'_form\').serialize(),\n')
    c.write('\t\tsuccess : function(json) {\n')
    c.write('\t\t\tvar obj = jQuery.parseJSON(json);\n')
    c.write('\t\t\tif(obj.success == 1) {\n')
    c.write('\t\t\t\t$(\'#ajax_'+ name.lower() +'\').html(success_icon + obj.message);\n')
    c.write('\t\t\t\t$(\'#ajax_'+ name.lower() +'\').removeClass(\'alert-warning\'); \n')
    c.write('\t\t\t\t$(\'#ajax_'+ name.lower() +'\').addClass(\'alert-success\'); \n')
    c.write('\t\t\t\tsetTimeout(function(){ \n')
    c.write('\t\t\t\t\tlocation.reload();\n')
    c.write('\t\t\t\t},1000);\n')
    c.write('\t\t\t} else {\n')
    c.write('\t\t\t\t$(\'#ajax_'+ name.lower() +'\').html(error_icon  + obj.message);\n')
    c.write('\t\t\t\t$(\'#ajax_'+ name.lower() +'\').removeClass(\'alert-warning\');\n')
    c.write('\t\t\t\t$(\'#ajax_'+ name.lower() +'\').addClass(\'alert-danger\');\n')
    c.write('\t\t\t}\n')
    c.write('\t\t},\n')
    c.write('\t\terror : function() {\n')
    c.write('\t\t\twindow.alert(\'#'+ name.lower() +' ERORR\');\n')
    c.write('\t\t}\n')
    c.write('\t});\n')
    c.write('});')
    c.close()

    print 'Modificado ./api/http/' + method + '.php'
    print 'Creado ./views/app/js/' + name.lower() + '.js'

def main():
    print ""
    arg = sys.argv
    if len(arg) == 3:
        model = arg[2].capitalize()
        controller = arg[2].lower() + 'Controller'
        view = arg[2].lower()
        count = 0

        if view in ['models','controllers','ocrend','firewall','debug','conexion']:
            print u'El módulo existe en el Kernel, no puede crearse.'
        else:
            api = False
            if 'm' in arg[1]:
                count += 1
                if 'a:post' in arg[1]:
                    write_api('post',view)
                    api = True
                elif 'a:get' in arg[1]:
                    write_api('get',view)
                    api = True

                check_file(R_MODELS,model,'.php',api)

            if 'v' in arg[1]:
                count += 1
                if 'm' in arg[1] and 'a:post' in arg[1]:
                    api = True
                elif 'm' in arg[1] and 'a:get' in arg[1]:
                    api = True

                check_file(R_VIEWS,view,'.phtml',api)

            if 'c' in arg[1]:
                count += 1
                check_file(R_CONTROLLERS,controller)

            if count == 0:
                print u"Módulo no encontrado, escribir en consola: \"python gen.py -ayuda\""

    elif len(arg) == 1:
        print u"Es necesario escribir los módulos a crear"
    else:
        if arg[1] == '-ayuda':
            print '======================= AYUDA ======================='
            print '-Crear Modelo: python gen.py m Modulo'
            print '-Crear Modelo y Petición GET API REST: python gen.py ma:get Modulo'
            print '-Crear Modelo y Petición POST API REST: python post.py ma:get Modulo\n'
            print '-Crear Vista: python gen.py v Modulo'
            print '-Crear Controlador: python gen.py c Modulo\n'
            print '-Crear Modelo y Vista: python gen.py mv Modulo'
            print '-Crear Modelo, Petición GET API REST y Vista: python gen.py mva:get Modulo'
            print '-Crear Modelo, Petición POST API REST y Vista: python post.py mva:get Modulo\n'
            print '-Crear Modelo y Controlador: python gen.py mc Modulo'
            print '-Crear Modelo, Petición GET API REST y Controlador: python gen.py mca:get Modulo'
            print '-Crear Modelo, Petición POST API REST y Controlado: python post.py mca:get Modulo\n'
            print '-Crear Modelo, Vista y Controlador: python gen.py mvc Modulo'
            print '-Crear Modelo, Petición GET API REST, Vista y Controlador: python gen.py mvca:get Modulo'
            print '-Crear Modelo, Petición POST API REST, Vista y Controlado: python post.py mvca:get Modulo\n'
            print '-Crear Controlador y Vista: python gen.py cv Modulo\n'
            print 'Para más información visitar www.ocrend.com/framework'
        else:
            print u"Es necesario escribir el nombre del módulo a crear"

if __name__ == '__main__':
    main()
