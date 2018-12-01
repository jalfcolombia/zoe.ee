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
 * 
 * PHP version 7
 * 
 * @category ZoeEE
 * @package  Controller
 * @author   Julian Lasso <jalasso69@misena.edu.co>
 * @license  https://github.com/jalfcolombia/zoe.ee/blob/master/LICENSE Apache2 License
 * @link     https://github.com/jalfcolombia/zoe.ee
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
 * @category   ZoeEE
 * @package    Controller
 * @subpackage FrontController
 * @author     Julian Lasso <jalasso69@misena.edu.co>
 * @license    https://github.com/jalfcolombia/zoe.ee/blob/master/LICENSE Apache2 License
 * @link       https://github.com/jalfcolombia/zoe.ee
 */
class FrontController
{

    /**
     * Constante pública para indicar que el ambito de ejecución del sistema es
     * de DESARROLLO
     */
    public const DEV = 'dev';

    /**
     * Constante pública para indicar que el ambito de ejecución del sistema es
     * de PRODUCCION
     */
    public const PROD = 'prod';

    /**
     * Constante pública para indicar que el ambito de ejecución del sistema es
     * de TESTEO
     */
    public const TEST = 'test';

    /**
     * Objeto para el manejo del caché del sistema
     *
     * @var Cache
     */
    private $_cache;

    /**
     * Objeto para el manejo de la configuración del sistema
     *
     * @var Config
     */
    private $_config;

    /**
     * Objeto para el manejo del ambito en que se encuentra el sistema
     *
     * @var string
     */
    private $_scope;

    /**
     * Objeto para el manejo de las rutas del sistema
     *
     * @var Routing
     */
    private $_routing;

    /**
     * Objeto para el manejo de las solicitudes al sistema
     *
     * @var Request
     */
    private $_request;

    /**
     * Objeto para el manejo de las sesiones del sistema
     *
     * @var Session
     */
    private $_session;

    /**
     * Objeto para el manejo de la internacionalización de los mensajes del sistema
     *
     * @var i18n
     */
    private $_i18n;

    /**
     * Ruta física del proyecto en el servidor
     *
     * @var string
     */
    private $_path;
    
    /**
     * Objeto para manejar la vista del sistema
     *
     * @var Response
     */
    private $_response;

    /**
     * Constructor del controlador frontal
     * 
     * @param string $path  Ruta del proyecto en el servidor
     * @param string $scope Ambito del entorno
     */
    public function __construct(string $path, string $scope)
    {
        try {
            $this->_path = $path . DIRECTORY_SEPARATOR;
            $this->_scope = $scope;
            $this->_request = new Request();
            $this->response = new Response($this->_path);
            $this->_cache = new Cache($this->_path);
            $this->_routing = new Routing(
                $this->_request->getServer('_PATH_INFO'),
                $this->_cache,
                $this->_path,
                $this->_request->getServer('REQUEST_METHOD'),
                $this->_request->isAjax(),
                $this->_scope
            );
            $this->_config = new Config(
                $this->_cache,
                $this->_scope,
                $this->_path,
                $this->_routing->getBundle(),
                $this->_routing->getProject()
            );
            $this->_i18n = new i18n(
                $this->_config->get('lang'),
                $this->_scope,
                $this->_cache,
                $this->_path,
                $this->_routing->getBundle(),
                $this->_routing->getProject()
            );
            $this->_session = new Session(
                $this->_config->get('session.name'),
                $this->_config->get('session.time')
            );
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
     * 
     * @return void
     */
    public function run(): void
    {
        try {
            $this->_middleware('getMiddlewareBefore');
            $this->_controller();
            $this->_middleware('getMiddlewareAfter');
            $this->_response();
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
     * Ejecuta los Middleware programados antes o después de ejecutar
     * el controlador solicitado
     * 
     * @param string $function Nombre de la función a ejecutar
     *                         getMiddlewareBefore o getMiddlewareAfter1
     * 
     * @return void
     */
    private function _middleware($function): void
    {
        $middleware = $this->_routing->$function();
        if (is_string($middleware) === true) {
            require $this->_path . $middleware . 'Middleware.php';
            $middleware = "\\" . $middleware . 'Middleware';
            eval("\$middleware = new {$middleware}();");
            $middleware->main(
                $this->_request,
                $this->_i18n,
                $this->_config,
                $this->_session,
                $this->_routing
            );
        } else if (is_array($middleware) === true) {
            foreach ($middleware as $mddlwr) {
                require $this->_path . $mddlwr . 'Middleware.php';
                $mddlwr = "\\" . $mddlwr . 'Middleware';
                eval("\$mddlwr = new {$mddlwr}();");
                $mddlwr->main(
                    $this->_request,
                    $this->_i18n,
                    $this->_config,
                    $this->_session,
                    $this->_routing
                );
            }
        } else {
            // DISPARAR ERROR
        }
        exit();
    }

    /**
     * Ejecuta el controlador y la acción solicitada en la URL
     * 
     * @return void
     */
    private function _controller(): void
    {
        $controller = $this->_routing->getController();
        if ($this->_routing->getAction() === null) {
            $controller->main(
                $this->_request, 
                $this->_i18n, 
                $this->_config, 
                $this->session, 
                $this->_routing
            );
        } else {
            $controller->$this->_routing->getAction()(
                $this->_request,
                $this->_i18n,
                $this->_config,
                $this->session,
                $this->_routing
            );
        }
    }

    /**
     * Respuesta de la aplicación al usuario
     * 
     * @return void
     */
    private function _response(): void
    {
        $this->response
            ->setVariables((array)$controller)
            ->setView($this->_routing->getView())
            ->render($this->_getPathView());
    }

    /**
     * Devuele el la dirección donde se encuentra la visa solicitada
     * 
     * @return void
     */
    private function _getPathView(): string
    {
        $answer = null;
        if ($this->_routing->getProject() === null) {
            $answer = $this->_routing->getBundle();
        } else {
            $answer = $this->_routing->getProject()
             . DIRECTORY_SEPARATOR
             . $this->_routing->getBundle();
        }
        return $answer . DIRECTORY_SEPARATOR;
    }
}
