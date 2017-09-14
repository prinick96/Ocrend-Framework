<?php

/*
 * This file is part of the Ocrend Framewok 2 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ocrend\Kernel\Router;

use Ocrend\Kernel\Router\IRouter;

/**
 * Encargado de controlar las URL Amigables en cada controlador del sistema, es independiente al Routing de Silex.
 * Define por defecto 3 rutas escenciales, controlador, método e id.
 *
 * @author Brayan Narváez <prinick@ocrend.com>
 */

final class Router implements IRouter {

    /**
     * @var array CONSTANTE con las reglas permitidas
     */
    const RULES = [
        'none', # Sin ninguna regla
        'letters', # Sólamente letras
        'alphanumeric', # Letras y números
        'url', # Con forma para URL (letras,números y el caracter -)
        'integer', # Sólamente números enteros
        'integer_positive', # Solamente números enteros positivos
        'float', # Sólamente números flotantes
        'float_positive' # Solamente números flotantes positivos
    ];

    /**
     * Colección de rutas existentes
     *
     * @var array 
     */
    private $routerCollection = array(
        '/controller' => 'home', # controlador por defecto
        '/method' => null, # método por defecto
        '/id' => null # id por defecto
    );

    /**
     * Colección de reglas para cada ruta existente
     *
     * @var array 
     */
    private $routerCollectionRules = array(
        '/controller' => 'letters',
        '/method' => 'none',
        '/id' => 'none'
    );

    /**
     * @var array
     */
    private $real_request = array();

    /**
     * @var string
     */
    private $requestUri;

    /**
     * __construct() 
     */
    public function __construct() {
        global $http;
        
        # Obtener las peticiones
        $this->requestUri = $http->query->get('routing');

        # Verificar las peticiones
        $this->checkRequests();
    }   

    /**
     * Coloca una regla destinada a una ruta, siempre y cuando esta regla exista.
     *
     * @param string $index : Índice de la ruta
     * @param string $rule : Nombre de la regla
     *
     * @throws \RuntimeException si la regla no existe
     */
    final private function setCollectionRule(string $index, string $rule) {
        # Verificar si la regla existe
        if (!in_array($rule, self::RULES)) {
            throw new \RuntimeException('La regla ' . $rule . ' no existe.');
        }
        # Definir la regla para la ruta
        $this->routerCollectionRules[$index] = $rule;
    }

    /**
     * Verifica las peticiones por defecto
     */
    final private function checkRequests() {
        # Verificar si existe peticiones
        if (null !== $this->requestUri) {
            $this->real_request = explode('/', $this->requestUri);
            $this->routerCollection['/controller'] = $this->real_request[0];
        }

        # Setear las siguientes rutas
        $this->routerCollection['/method'] = array_key_exists(1, $this->real_request) ? $this->real_request[1] : null;
        $this->routerCollection['/id'] = array_key_exists(2, $this->real_request) ? $this->real_request[2] : null;
    }

    /**
     * Crea una nueva ruta.
     *
     * @param string $index : Índice de la ruta
     * @param string $rule : Nombre de la regla, por defecto es ninguna "none"
     *
     * @throws \RuntimeException si no puede definirse la ruta
     */
    final public function setRoute(string $index, string $rule = 'none') {
        # Nombres de rutas no permitidos
        if (in_array($index, ['/controller', '/method', '/id'])) {
            throw new \RuntimeException('No puede definirse ' . $index . ' como índice en la ruta.');
        }

        # Sobreescribir
        unset(
            $this->routerCollection[$index],
            $this->routerCollectionRules[$index]
        );
            
        # Definir la ruta y regla
        $lastRoute = sizeof($this->routerCollection);
        $this->routerCollection[$index] = array_key_exists($lastRoute, $this->real_request) ? $this->real_request[$lastRoute] : null;
        $this->setCollectionRule($index, $rule);
    }
    
    /**
     * Obtiene el valor de una ruta según la regla que ha sido definida y si ésta existe.
     *
     * @param string $index : Índice de la ruta
     *
     * @throws \RuntimeException si la ruta no existe o si no está implementada la regla
     * @return mixed : Valor de la ruta solicitada
     */
    final public function getRoute(string $index) {
          # Verificar existencia de ruta
        if (!array_key_exists($index, $this->routerCollection)) {
            throw new \RuntimeException('La ruta ' . $index . ' no está definida en el controlador.');
        }

        # Obtener la ruta nativa sin reglas
        $ruta = $this->routerCollection[$index];

        # Retornar ruta con la regla definida aplicada
        switch ($this->routerCollectionRules[$index]) {
            case 'none':
                return $ruta;
            case 'letters':
                return preg_match("/^[a-zA-Z ]*$/", $ruta) ? $ruta : null;
            case 'alphanumeric':
                return preg_match('/^[a-zA-Z0-9 ]*$/', $ruta) ? $ruta : null;
            case 'url':
                return preg_match('/^[a-zA-Z0-9- ]*$/', $ruta) ? $ruta : null;
            case 'integer':
                return is_numeric($ruta) ? (int) $ruta : null;
            case 'float':
                return is_numeric($ruta) ? (float) $ruta : null;
            case 'integer_positive':
                return (is_numeric($ruta) && $ruta >= 0) ? (int) $ruta : null;
            case 'float_positive':
                return (is_numeric($ruta) && $ruta >= 0) ? (float) $ruta : null;
            default:
                throw new \RuntimeException('La regla ' . $this->routerCollectionRules[$index] . ' existe en RULES pero no está implementada.');
            break;
        }
    }

    /**
     * Obtiene el nombre del controlador.
     * 
     * @return string controlador.
     */    
    final public function getController() {
        return $this->routerCollection['/controller'];
    }

    /**
     * Obtiene el método
     * 
     * @return string con el método.
     *           null si no está definido.
     */
    final public function getMethod() {
        return $this->routerCollection['/method'];
    }   

    /**
     * Obtiene el id
     *
     * @param bool $with_rules : true para obtener el id con reglas definidas para números mayores a 0
     *                           false para obtener el id sin reglas definidas
     * 
     * @return int|null con el id
     *           int con el id si usa reglas.
     *           null si no está definido.
     */
    final public function getId(bool $with_rules = false) {
        $id = $this->routerCollection['/id'];
        if ($with_rules) {
            return (null !== $id && is_numeric($id) && $id > 0) ? (int) $id : null;
        }

        return $id;
    }

    /**
     * Ejecuta el controlador solicitado por la URL.
     * Si este no existe, ejecutará errorController.
     * Si no se solicita ningún controlador, ejecutará homeController.
     */
    final public function executeController() {
        # Definir controlador
        if (null != ($controller = $this->getController())) {
            $controller = $controller . 'Controller';

            if (!is_readable('app/controllers/' . $controller . '.php')) {
                $controller = 'errorController';
            }

        } else {
            $controller = 'errorController';
        }  

        $controller = 'app\\controllers\\' . $controller;    

        new $controller($this);      
    }

}