# -*- coding: utf-8 -*-
import sys
import os

'''
  * Ocrend Framework - PHP Code Generator
  * @author Brayan Narvaez (Prinick) <prinick@ocrend.com> <youtube.com/user/prinick96>
  * @copyright 2016 - Ocrend Software
  ****************************************************************************************************
  ****************************************************************************************************
  **** DESCONTINUADO, mejor utilizar gen.php, tiene más posibilidades y no requiere python ***********
  ****************************************************************************************************
  ****************************************************************************************************
'''

'''
R_MODELS = './core/models/'
R_CONTROLLERS = './core/controllers/'
R_VIEWS = './templates/'

def create_file(route,filename,ext,api,crud,php_method):
    if ext == '.phtml':
        if not os.path.isdir(route + filename + '/'):
            os.mkdir(route + filename + '/')

        if True == crud:
            e = open(route + filename + '/' + php_method + ext,'a')
        else:
            e = open(route + filename + '/' + filename + ext,'a')
    else:
        e = open(route + filename + ext,'a')

    if route == R_MODELS:
        if True == crud:
            amodel = open('./generator/python/crud/m.g','r')
        else:
            if api:
                amodel = open('./generator/python/ma.g','r')
            else:
                amodel = open('./generator/python/m.g','r')

        content = amodel.read()
        amodel.close()
        content = content.replace('{{model}}',filename)
        content = content.replace('{{view}}',filename.replace('Controller', '').lower())
        e.write(content)
    elif route == R_CONTROLLERS:
        if True == crud:
            acontroller = open('./generator/python/crud/c.g','r')
        else:
            acontroller = open('./generator/python/c.g','r')

        content = acontroller.read()
        acontroller.close()
        content = content.replace('{{controller}}',filename)
        content = content.replace('{{model}}',filename.replace('Controller', '').capitalize())
        content = content.replace('{{view}}',filename.replace('Controller', ''))
        e.write(content)
    else:
        route = route + filename + '/'
        if True == crud:
            if 'editar' == php_method:
                creado = route + 'editar' + ext
                aview = open('./generator/python/crud/v_edit.g','r')
            elif 'crear' == php_method:
                creado = route + 'crear' + ext
                aview = open('./generator/python/crud/v_add.g','r')
            else:
                creado = route + filename + ext
                aview = open('./generator/python/crud/v_list.g','r')
        else:
            creado = route + filename + ext
            if api:
                aview = open('./generator/python/va.g','r')
            else:
                aview = open('./generator/python/v.g','r')

        content = aview.read()
        content = content.replace('{{action}}',filename.lower())
        content = content.replace('{{model}}',filename.capitalize())
        aview.close()
        e.write(content)

    e.close()
    if ext == '.phtml':
        print (creado)
    else:
        print ("Creado " + route + filename + ext)

def check_file(route,filename,ext = '.php',api = False,crud = False,php_method = 'Foo'):
    if os.path.exists(route + filename + ext) or os.path.exists(route + filename + '/' + filename + ext):
        print ("ERROR: El archivo " + filename + ext + " ya existe")
    else:
        create_file(route,filename,ext,api,crud,php_method)

def write_api(method,name,crud,php_method):

    e = open('./api/http/' + method + '.php','a+')
    e.write('\n\n/**\n')
    e.write('\t* ¿que hace (el modelo que se invoca desde aqui)?\n')
    e.write('\t* @return Devuelve un json con información acerca del éxito o posibles errores. \n')
    e.write('*/\n')

    if method == 'get':
        m = '$app->get(\'/'
    else:
        m = '$app->post(\'/'

    if True == crud:
        e.write(m + name.lower() + '/' + php_method.lower() + '\',function($request, $response) {\n\n')
    else:
        e.write(m + name.lower() + '\',function($request, $response) {\n\n')

    e.write('\t$model = new '  + name.capitalize() + ';\n')
    e.write('\t$response->withJson($model->' + php_method.lower() + '($_' + method.upper() + '));\n\n')
    e.write('\treturn $response;')
    e.write('\n});')
    e.close()

    ajs = open('./generator/python/js.g','r')
    content = ajs.read()
    ajs.close()

    content = content.replace('{{view}}',name.lower())
    content = content.replace('{{method}}',method.upper())

    if True == crud:
        if not os.path.isdir('./views/app/js/' + name.lower() + '/'):
            os.mkdir('./views/app/js/' + name.lower() + '/')

        c = open('./views/app/js/' + name.lower() + '/' + php_method.lower() +'.js','a')
        content = content.replace('{{api_rest}}',name.lower() + '/' + php_method.lower())
    else:
        c = open('./views/app/js/' + name.lower() + '.js','a')
        content = content.replace('{{api_rest}}',name.lower())

    c.write(content)
    c.close()

    print ('Modificado ./api/http/' + method + '.php')
    if True == crud:
        print ('Creado ./views/app/js/' + name.lower() + '/' + php_method.lower() + '.js')
    else:
        print ('Creado ./views/app/js/' + name.lower() + '.js')

def main():
    print ("")
    arg = sys.argv
    if len(arg) == 3:
        model = arg[2].capitalize()
        controller = arg[2].lower() + 'Controller'
        view = arg[2].lower()
        count = 0

        if view in ['models','controllers','ocrend','firewall','debug','conexion','router','helper','arrays','files','strings','paypal','bootstrap']:
            print ('El modulo existe en el Kernel, no puede crearse.')
        else:

            if arg[1] == 'crud':
                write_api('post',view,True,'editar')
                write_api('post',view,True,'crear')
                check_file(R_MODELS,model,'.php',True,True)
                check_file(R_CONTROLLERS,controller,'.php',True,True)
                check_file(R_VIEWS,view,'.phtml',True,True,'editar')
                check_file(R_VIEWS,view,'.phtml',True,True,'crear')
                check_file(R_VIEWS,view,'.phtml',True,True,view)
            else:
                api = False
                if 'm' in arg[1]:
                    count += 1
                    if 'a:post' in arg[1]:
                        write_api('post',view,False,'foo')
                        api = True
                    elif 'a:get' in arg[1]:
                        write_api('get',view,False,'foo')
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
            print ('\n- RECOMENDADO USAR EL GENERADOR gen.php, VER DOC EN PAGINA OFICIAL.\n')
            print ('\n-Crear CRUD COMPLETO: python gen.py crud Modulo')
            print ('-Crear Modelo: python gen.py m Modulo')
            print ('-Crear Modelo y Peticion GET API REST: python gen.py ma:get Modulo')
            print ('-Crear Modelo y Peticion POST API REST: python gen.py ma:post Modulo\n')
            print ('-Crear Vista: python gen.py v Modulo')
            print ('-Crear Controlador: python gen.py c Modulo\n')
            print ('-Crear Modelo y Vista: python gen.py mv Modulo')
            print ('-Crear Modelo, Peticion GET API REST y Vista: python gen.py mva:get Modulo')
            print ('-Crear Modelo, Peticion POST API REST y Vista: python gen.py mva:post Modulo\n')
            print ('-Crear Modelo y Controlador: python gen.py mc Modulo')
            print ('-Crear Modelo, Peticion GET API REST y Controlador: python gen.py mca:get Modulo')
            print ('-Crear Modelo, Peticion POST API REST y Controlado: python gen.py mca:post Modulo\n')
            print ('-Crear Modelo, Vista y Controlador: python gen.py mvc Modulo')
            print ('-Crear Modelo, Peticion GET API REST, Vista y Controlador: python gen.py mvca:get Modulo')
            print ('-Crear Modelo, Peticion POST API REST, Vista y Controlado: python gen.py mvca:post Modulo\n')
            print ('-Crear Controlador y Vista: python gen.py cv Modulo\n')
            print ('Para mas informacion visitar framework.ocrend.com/generador/')
        else:
            print ("Es necesario escribir el nombre del modulo a crear")

'''

def main():
    print ('\nEste generador ha sido descontinuado, revisar el generador PHP, en la documentacion oficial. \n\nEs algo tan facil como escribir:\n- php gen.php -ayuda')

if __name__ == '__main__':
    main()
