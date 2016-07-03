# -*- coding: utf-8 -*-
import sys
import os

'''
  * Ocrend Framework - PHP Code Generator
  * @author Brayan Narvaez (Prinick) <prinick@ocrend.com> <youtube.com/user/prinick96>
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
        if api:
            amodel = open('./generator/ma.g','r')
        else:
            amodel = open('./generator/m.g','r')

        content = amodel.read()
        amodel.close()
        e.write(content.replace('{{model}}',filename))
    elif route == R_CONTROLLERS:
        acontroller = open('./generator/c.g','r')
        content = acontroller.read()
        acontroller.close()
        content = content.replace('{{controller}}',filename)
        content = content.replace('{{view}}',filename.replace('Controller', ''))
        e.write(content)
    else:
        route = route + filename + '/'
        if api:
            aview = open('./generator/va.g','r')
            content = aview.read()
            content = content.replace('{{action}}',filename.lower())
            aview.close()
        else:
            aview = open('./generator/v.g','r')
            content = aview.read()
            aview.close()

        e.write(content)

    e.close()
    print ("Creado " + route + filename + ext)

def check_file(route,filename,ext = '.php',api = False):
    if os.path.exists(route + filename + ext) or os.path.exists(route + filename + '/' + filename + ext):
        print ("ERROR: El archivo " + filename + ext + " ya existe")
    else:
        create_file(route,filename,ext,api)

def write_api(method,name):

    e = open('./api/http/' + method + '.php','a+')
    e.write('\n/**\n')
    e.write('\t* ¿que hace (el modelo que se invoca desde aqui)?\n')
    e.write('\t* @return ¿que retorna?, ¡un json por favor! \n')
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
    ajs = open('./generator/js.g','r')
    content = ajs.read()
    ajs.close()
    content = content.replace('{{view}}',name.lower())
    content = content.replace('{{method}}',method.upper())
    c.write(content)
    c.close()

    print ('Modificado ./api/http/' + method + '.php')
    print ('Creado ./views/app/js/' + name.lower() + '.js')

def main():
    print ("")
    arg = sys.argv
    if len(arg) == 3:
        model = arg[2].capitalize()
        controller = arg[2].lower() + 'Controller'
        view = arg[2].lower()
        count = 0

        if view in ['models','controllers','ocrend','firewall','debug','conexion','router','helper','arrays','files','strings']:
            print ('El modulo existe en el Kernel, no puede crearse.')
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
                print ("Modulo no encontrado, escribir en consola: \"python gen.py -ayuda\"")

    elif len(arg) == 1:
        print ("Es necesario escribir los modulos a crear")
    else:
        if arg[1] == '-ayuda':
            print ('======================= AYUDA =======================')
            print ('-Crear Modelo: python gen.py m Modulo')
            print ('-Crear Modelo y Peticion GET API REST: python gen.py ma:get Modulo')
            print ('-Crear Modelo y Peticion POST API REST: python post.py ma:get Modulo\n')
            print ('-Crear Vista: python gen.py v Modulo')
            print ('-Crear Controlador: python gen.py c Modulo\n')
            print ('-Crear Modelo y Vista: python gen.py mv Modulo')
            print ('-Crear Modelo, Peticion GET API REST y Vista: python gen.py mva:get Modulo')
            print ('-Crear Modelo, Peticion POST API REST y Vista: python post.py mva:get Modulo\n')
            print ('-Crear Modelo y Controlador: python gen.py mc Modulo')
            print ('-Crear Modelo, Peticion GET API REST y Controlador: python gen.py mca:get Modulo')
            print ('-Crear Modelo, Peticion POST API REST y Controlado: python post.py mca:get Modulo\n')
            print ('-Crear Modelo, Vista y Controlador: python gen.py mvc Modulo')
            print ('-Crear Modelo, Peticion GET API REST, Vista y Controlador: python gen.py mvca:get Modulo')
            print ('-Crear Modelo, Peticion POST API REST, Vista y Controlado: python post.py mvca:get Modulo\n')
            print ('-Crear Controlador y Vista: python gen.py cv Modulo\n')
            print ('Para mas informacion visitar framework.ocrend.com')
        else:
            print ("Es necesario escribir el nombre del modulo a crear")

if __name__ == '__main__':
    main()
