<?php

/*
 * This file is part of the Ocrend Framewok 3 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 * @author Brayan Narváez <prinick@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

use app\models as Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ocrend\Kernel\Models\ModelsException;

/**
 * Convertir esta api en RESTFULL para recibir JSON
 */
$app->before(function () use ($app) {
    try {
        global $config, $http;
        
        # Verificar si la api no está activa
        if(!$config['api']['active']) {
            throw new ModelsException('Servicio inactivo');
        }

        # Recibir JSON
        if (0 === strpos($http->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($http->getContent(), true);
            $http->request->replace(is_array($data) ? $data : array());
        }
    } catch(ModelsException $e) {
        return $app->json(array(
            'success' => 0,
            'message' => $e->getMessage()
        ));
    }
});

/**
 * Servidores autorizados para consumir la api.
 */
$app->after(function (Request $request, Response $response) {
    global $config;

    $response->headers->set('Access-Control-Allow-Origin', $config['api']['origin']);
});