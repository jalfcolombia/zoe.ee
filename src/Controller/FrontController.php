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
     * Controlador en ejecución
     *
     * @var object
     */
    private $controller;

    /**
     * Constructor del controlador frontal
     *
     * @param string $path  Ruta del proyecto en el servidor
     * @param string $scope Ambito del entorno
     */
    public function __construct(string $path, string $scope)
    {
        $this->requestOptions();
        try {
            $this->path = $path;
            $this->scope = $scope;
            $this->request = new Request();
            $this->response = new Response($this->path);
            $this->cache = new Cache($this->path);
            $this->routing = new Routing(
                $this->request->getServer('PATH_INFO'),
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
            header("Content-type: application/json; charset=utf-8");
            http_response_code(500);
            $json_data = array(
                'file' => $exc->getFile(),
                'line' => $exc->getLine(),
                'error' => $exc->getCode(),
                'message' => $exc->getMessage(),
                'trace' => $exc->getTrace()
            );
            echo json_encode($json_data);
            exit();
        }
    }

    private function requestOptions(): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: DNT,X-CustomHeader,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type,Content-Range,Range,Options,Accept,Authorization,Origin');
        header('Access-Control-Max-Age: 1728000');

        if (strtoupper($_SERVER['REQUEST_METHOD']) === 'OPTIONS') {
            header('Content-Type: text/plain; charset=utf-8');
            header('Content-Length: 0');
            http_response_code(204);
            exit();
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
            // $this->middleware('getMiddlewareAfter');
            $this->response();
        } catch (\ErrorException | \Exception | \RuntimeException | \PDOException | \Error $exc) {
            http_response_code(500);
            if ($this->request->isAjax() === true) {
                header('Content-type: application/json; charset=utf-8');
                echo json_encode(
                    array(
                        'File' => $exc->getFile(),
                        'Line' => $exc->getLine(),
                        'Error' => $exc->getCode(),
                        'Message' => $exc->getMessage(),
                        'Trace' => $exc->getTrace()
                    )
                );
                // exit();
            } else {
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

    /**
     * Ejecuta los Middleware programados antes o después de ejecutar el controlador solicitado
     *
     * @param string $function Nombre de la función a ejecutar getMiddlewareBefore o getMiddlewareAfter1
     *
     * @return void
     */
    private function middleware($function): void
    {
        $middleware = $this->routing->{$function}();
        if (is_string($middleware) === true) {
            eval("\$middleware = new \\{$middleware}(\$this->cache);");
            $middleware->main(
                $this->request,
                $this->i18n,
                $this->config,
                $this->session,
                $this->routing
            );
        } elseif (is_array($middleware) === true) {
            foreach ($middleware as $mddlwr) {
                eval("\$mddlwr = new \\{$mddlwr}(\$this->cache);");
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
        // exit();
    }

    /**
     * Ejecuta el controlador y la acción solicitada en la URL
     *
     * @return void
     */
    private function controller(): void
    {
        $this->controller = $this->routing->getController();
        if ($this->routing->getAction() === null) {
            $this->controller->main(
                $this->request,
                $this->i18n,
                $this->config,
                $this->session,
                $this->routing
            );
        } else {
            $this->controller->{$this->routing->getAction()}(
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
            ->setVariables((array) $this->controller)
            ->setView((($this->routing->hasView()) ? $this->routing->getView() : $this->controller->getView()))
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

    static public function getRealIP($void = null)
    {
        $headers = array(
            'HTTP_VIA',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED',
            'HTTP_CLIENT_IP',
            'HTTP_HTTP_CLIENT_IP',
            'HTTP_FORWARDED_FOR_IP',
            'VIA',
            'X_FORWARDED_FOR',
            'FORWARDED_FOR',
            'X_FORWARDED',
            'FORWARDED',
            'CLIENT_IP',
            'FORWARDED_FOR_IP',
            'HTTP_XPROXY_CONNECTION',
            'HTTP_PROXY_CONNECTION',
            'HTTP_X_REAL_IP',
            'HTTP_X_PROXY_ID',
            'HTTP_USERAGENT_VIA',
            'HTTP_HTTP_PC_REMOTE_ADDR',
            'HTTP_X_CLUSTER_CLIENT_IP'
        );

        foreach ($headers as $header)
            if (isset($_SERVER[$header]) && !empty($_SERVER[$header]))
                return $_SERVER[$header];

        if (trim($_SERVER['SERVER_ADDR']) == trim($_SERVER['REMOTE_ADDR']))
            return $_SERVER['SERVER_ADDR'];

        return $_SERVER['REMOTE_ADDR'];
    }

}
