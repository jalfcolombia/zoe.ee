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
use ZoeEE\i18n\i18n;
use ZoeEE\Request\Request;
use ZoeEE\Routing\Routing;
use ZoeEE\View\View;
use ZoeEE\Session\Session;

/**
 *
 * @author Julian Lasso <jalasso69@misena.edu.co>
 * @package ZoeEE
 * @subpackage Cache
 */
class FrontController
{

    /**
     *
     * @var Cache
     */
    private $cache;

    /**
     *
     * @var Config
     */
    private $config;

    /**
     *
     * @var string
     */
    private $scope;

    /**
     *
     * @var Routing
     */
    private $routing;

    /**
     *
     * @var Request
     */
    private $request;

    /**
     *
     * @var Session
     */
    private $session;

    /**
     *
     * @var i18n
     */
    private $i18n;

    /**
     *
     * @var string
     */
    private $path;

    /**
     *
     * @var View
     */
    private $view;

    /**
     *
     * @var string
     */
    private $scope;

    /**
     *
     * @param string $path
     *            Ruta del proyecto en el servidor
     * @param string $scope
     *            Ambito del entorno
     */
    public function __construct(string $path, string $scope)
    {
        $this->path = $path;
        $this->scope = $scope;
        $this->request = new Request();
        $this->cache = new Cache($path);
        $this->routing = new Routing($this->request->getServer('PATH_INFO'), $this->cache, $path, $scope);
        $this->config = new Config($this->cache, $scope, $path, $this->routing->getBundle());
        $this->i18n = new i18n($this->config->get('lang'), $scope, $this->cache, $path, $this->routing->getBundle());
        $this->session = new Session($this->config->get('session.name'), $this->config->get('session.time'));
        $this->view = new View();
    }

    public function run()
    {
        $controller = $this->routing->getController();
        $controller->main($this->request, $this->i18n, $this->config, $this->session, $this->routing);
        $this->view->SetView($this->path . $this->routing->getView())
            ->SetVariables((array) $controller)
            ->Render();
    }
}
