<?php

/**
 * This file is part of the ZoeEE package.
 *
 * (c) Julian Lasso <jalasso69@misena.edu.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
     * @param string $path
     *            Ruta del proyecto en el servidor
     * @param string $scope
     *            Ambito del entorno
     */
    public function __construct(string $path, string $scope)
    {
        try {
            $this->path = $path;
            $this->scope = $scope;
            $this->request = new Request();
            $this->response = new Response($path);
            $this->cache = new Cache($path);
            $this->routing = new Routing($this->request->getServer('PATH_INFO'), $this->cache, $path, $scope);
            $this->config = new Config($this->cache, $scope, $path, $this->routing->getBundle());
            $this->routing->setProject($this->config->get('project'));
            $this->i18n = new i18n($this->config->get('lang'), $scope, $this->cache, $path, $this->routing->getBundle());
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
            $controller->main($this->request, $this->i18n, $this->config, $this->session, $this->routing);
            $this->response->setView($this->routing->getView())
                ->setVariables((array) $controller)
                ->render($this->routing->getBundle() . DIRECTORY_SEPARATOR);
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
