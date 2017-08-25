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

/**
 * Mínimos requisitos para que un Router funcione adentro del framework.
 *
 * @author Brayan Narváez <prinick@ocrend.com>
 */

interface IRouter {
    public function setRoute(string $index, string $rule);
    public function getRoute(string $index);
    public function getController();
    public function getMethod();
    public function getId(bool $with_rules);
    public function executeController();
}