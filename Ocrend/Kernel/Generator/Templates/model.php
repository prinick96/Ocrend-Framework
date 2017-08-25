<?php

/*
 * This file is part of the Ocrend Framewok 2 package.
 *
 * (c) Ocrend Software <info@ocrend.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace app\models;

use app\models as Model;
use Ocrend\Kernel\Models\Models;
use Ocrend\Kernel\Models\IModels;
use Ocrend\Kernel\Models\ModelsException;
use Ocrend\Kernel\Models\Traits\DBModel;
use Ocrend\Kernel\Router\IRouter;

/**
 * Modelo {{model}}
 *
 * @author {{author}} <{{author_email}}>
 */

class {{model}} extends Models implements IModels {
    {{trait_db_model}}

    {{content}}

    /**
      * __construct()
    */
    public function __construct(IRouter $router = null) {
        parent::__construct($router);
        {{trait_db_model_construct}}
    }

    /**
      * __destruct()
    */ 
    public function __destruct() {
        parent::__destruct();
        {{trait_db_model_destruct}}
    }
}