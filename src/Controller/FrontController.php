<?php

/**
 * Copyright 2018 Servicio Nacional de Aprendizaje - SENA
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace ZoeEE\Controller;

use ZoeEE\Cache\Cache;
use ZoeEE\Config\Config;
use ZoeEE\Request\Request;
use ZoeEE\Response\Response;
use ZoeEE\Routing\Routing;
use ZoeEE\Session\Session;
use ZoeEE\i18n\i18n;

/**
 * Clase del controlador frontal
 *
 * @author Julian Lasso <jalasso69@misena.edu.co>
 * @package ZoeEE
 * @subpackage Cache
 */
class FrontController
{

    public const DEV = 'dev';
    public const PROD = 'prod';
    public const TEST = 'test';

    /**
     * Objeto para el manejo del caché del sistema
     *
     * @var Cache
     */
    private $cache;

    /**
     * Objeto para el manejo de la configuración del sistema
     *
     * @var Config
     */
    private $config;

    /**
     * Objeto para el manejo del ambito en que se encuentra el sistema
     *
     * @var string
     */
    private $scope;

    /**
     * Objeto para el manejo de las rutas del sistema
     *
     * @var Routing
     */
    private $routing;

    /**
     * Objeto para el manejo de las solicitudes al sistema
     *
     * @var Request
     */
    private $request;

    /**
     * Objeto para el manejo de las sesiones del sistema
     *
     * @var Session
     */
    private $session;

    /**
     * Objeto para el manejo de la internacionalización de los mensajes del sistema
     *
     * @var i18n
     */
    private $i18n;

    /**
     * Ruta física del proyecto en el servidor
     *
     * @var string
     */
    private $path;

    /**
     * Objeto para manejar la vista del sistema
     *
     * @var Response
     */
    private $response;

    /**
     * Constructor del controlador frontal
     *
     * @param string $path Ruta del proyecto en el servidor
     * @param string $scope Ambito del entorno
     */
    public function __construct(string $path, string $scope)
    {
        try {
            $this->path = $path;
            $this->scope = $scope;
            $this->request = new Request();
            $this->response = new Response($path);
            $this->cache = new Cache($path);
            $this->routing = new Routing($this->request->getServer('PATH_INFO'), $this->cache, $path, $this->request->getServer('REQUEST_METHOD'), $this->request->isAjax(), $scope);
            $this->config = new Config($this->cache, $scope, $path, $this->routing->getBundle(), $this->routing->getProject());
            $this->i18n = new i18n($this->config->get('lang'), $scope, $this->cache, $path, $this->routing->getBundle(), $this->routing->getProject());
            $this->session = new Session($this->config->get('session.name'), $this->config->get('session.time'));
        } catch (\ErrorException | \Exception $exc) {
            echo 'File: ' . $exc->getFile() . '<br>';
            echo 'Line: ' . $exc->getLine() . '<br>';
            echo 'Error: ' . $exc->getCode() . '<br>';
            echo 'Message: ' . $exc->getMessage() . '<br>';
            echo '<pre>';
            print_r($exc->getTrace());
            echo '</pre>';
        }
    }

    /**
     * Método para correr el sistema con el proyecto configurado
     */
    public function run(): void
    {
        try {
            $controller = $this->routing->getController();
            if ($this->routing->getAction() === null) {
                $controller->main($this->request, $this->i18n, $this->config, $this->session, $this->routing);
            } else {
                //$action = $this->routing->getAction();
                $controller->$this->routing->getAction()($this->request, $this->i18n, $this->config, $this->session, $this->routing);
            }
            $this->response->setView($this->routing->getView())
                ->setVariables((array)$controller)
                ->render((($this->routing->getProject() === null) ? $this->routing->getBundle() : $this->routing->getProject() . DIRECTORY_SEPARATOR . $this->routing->getBundle()) . DIRECTORY_SEPARATOR);
        } catch (\ErrorException | \Exception $exc) {
            echo 'File: ' . $exc->getFile() . '<br>';
            echo 'Line: ' . $exc->getLine() . '<br>';
            echo 'Error: ' . $exc->getCode() . '<br>';
            echo 'Message: ' . $exc->getMessage() . '<br>';
            echo '<pre>';
            print_r($exc->getTrace());
            echo '</pre>';
        }
    }
}
