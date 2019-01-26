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
 * PHP version 7.2
 *
 * @category Controller
 * @package  ZoeEE
 * @author   Julian Lasso <jalasso69@misena.edu.co>
 * @license  https://github.com/jalfcolombia/zoe.ee/blob/master/LICENSE Apache2
 * @link     https://github.com/jalfcolombia/zoe.ee
 */

namespace ZoeEE\Controller;

use ZoeEE\i18n\i18n;
use ZoeEE\Cache\Cache;
use ZoeEE\Config\Config;
use ZoeEE\Request\Request;
use ZoeEE\Routing\Routing;
use ZoeEE\Session\Session;
use ZoeEE\Response\Response;

/**
 * Clase del controlador frontal
 *
 * @category   Controller
 * @package    ZoeEE
 * @subpackage FrontController
 * @author     Julian Lasso <jalasso69@misena.edu.co>
 * @license    https://github.com/jalfcolombia/zoe.ee/blob/master/LICENSE Apache2
 * @link       https://github.com/jalfcolombia/zoe.ee
 */
class FrontController
{

    /**
     * Constante pública para indicar que el ambito de ejecución del sistema es de DESARROLLO
     */
    public const DEV = 'dev';

    /**
     * Constante pública para indicar que el ambito de ejecución del sistema es de PRODUCCION
     */
    public const PROD = 'prod';

    /**
     * Constante pública para indicar que el ambito de ejecución del sistema es de TESTEO
     */
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
     * @param string $path  Ruta del proyecto en el servidor
     * @param string $scope Ambito del entorno
     */
    public function __construct(string $path, string $scope)
    {
        try {
            $this->path = $path . DIRECTORY_SEPARATOR;
            $this->scope = $scope;
            $this->request = new Request();
            $this->response = new Response($this->path);
            $this->cache = new Cache($this->path);
            $this->routing = new Routing(
                $this->request->getServer('_PATH_INFO'),
                $this->cache,
                $this->path,
                $this->request->getServer('REQUEST_METHOD'),
                $this->request->isAjax(),
                $this->scope
            );
            $this->config = new Config(
                $this->cache,
                $this->scope,
                $this->path,
                $this->routing->getBundle(),
                $this->routing->getProject()
            );
            $this->i18n = new i18n(
                $this->config->get('lang'),
                $this->scope,
                $this->cache,
                $this->path,
                $this->routing->getBundle(),
                $this->routing->getProject()
            );
            $this->session = new Session(
                $this->config->get('session.name'),
                $this->config->get('session.time')
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
            $this->middleware('getMiddlewareBefore');
            $this->controller();
            $this->middleware('getMiddlewareAfter');
            $this->response();
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
     * Ejecuta los Middleware programados antes o después de ejecutar el controlador solicitado
     *
     * @param string $function Nombre de la función a ejecutar getMiddlewareBefore o getMiddlewareAfter1
     *
     * @return void
     */
    private function middleware($function): void
    {
        $middleware = $this->routing->$function();
        if (is_string($middleware) === true) {
            include $this->path . $middleware . 'Middleware.php';
            $middleware = "\\" . $middleware . 'Middleware';
            eval("\$middleware = new {$middleware}();");
            $middleware->main(
                $this->request,
                $this->i18n,
                $this->config,
                $this->session,
                $this->routing
            );
        } elseif (is_array($middleware) === true) {
            foreach ($middleware as $mddlwr) {
                include $this->path . $mddlwr . 'Middleware.php';
                $mddlwr = "\\" . $mddlwr . 'Middleware';
                eval("\$mddlwr = new {$mddlwr}();");
                $mddlwr->main(
                    $this->request,
                    $this->i18n,
                    $this->config,
                    $this->session,
                    $this->routing
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
    private function controller(): void
    {
        $controller = $this->routing->getController();
        if ($this->routing->getAction() === null) {
            $controller->main(
                $this->request,
                $this->i18n,
                $this->config,
                $this->session,
                $this->routing
            );
        } else {
            $controller->$this->routing->getAction()(
                $this->request,
                $this->i18n,
                $this->config,
                $this->session,
                $this->routing
            );
        }
    }

    /**
     * Respuesta de la aplicación al usuario
     *
     * @return void
     */
    private function response(): void
    {
        $this->response
            ->setVariables((array)$controller)
            ->setView($this->routing->getView())
            ->render($this->getPathView());
    }

    /**
     * Devuele el la dirección donde se encuentra la visa solicitada
     *
     * @return void
     */
    private function getPathView(): string
    {
        $answer = null;
        if ($this->routing->getProject() === null) {
            $answer = $this->routing->getBundle();
        } else {
            $answer = $this->routing->getProject() . DIRECTORY_SEPARATOR . $this->routing->getBundle();
        }
        return $answer . DIRECTORY_SEPARATOR;
    }
}
