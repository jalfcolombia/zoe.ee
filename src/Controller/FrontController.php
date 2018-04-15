<?php
namespace ZoeEE\Controller;

use ZoeEE\i18n\i18n;
use ZoeEE\View\View;
use ZoeEE\Cache\Cache;
use ZoeEE\Config\Config;
use ZoeEE\Routing\Routing;
use ZoeEE\Request\Request;
use ZoeEE\Session\Session;

class FrontController
{

    private const DIR = '.cache/';

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
     * @param string $path
     *            Ruta del proyecto en el servidor
     */
    public function __construct(string $path)
    {
        $this->path = $path;
        $this->i18n = new i18n();
        $this->view = new View();
        $this->routing = new Routing();
        $this->request = new Request();
        $this->cache = new Cache($path, self::DIR);
        $this->config = new Config($this->cache);
        $this->session = new Session($this->config->Get('session.name'), $this->config->Get('session.time'));
    }

    /**
     *
     * @param string $scope
     *            Espacio de trabajo dev o prod
     */
    public function Run(string $scope)
    {
        $this->scope = $scope;
        $controller = $this->routing->GetController();
        $controller->Main($this->request, $this->i18n, $this->config, $this->session, $this->routing);
        $this->view->SetView($this->path . $this->routing->GetView())
            ->SetVariables((array) $controller)
            ->Render();
    }
}