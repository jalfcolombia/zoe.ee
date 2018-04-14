<?php

namespace ZoeEE\Controller;

use ZoeEE\i18n\i18n;
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
  private $routing;
  private $request;
  private $session;
  private $i18n;
  private $path;

  public function __construct($path)
  {
    $this->path = $path;
    $this->cache = new Cache($path, self::DIR);
    $this->config = new Config($this->cache);
    $this->routing = new Routing();
    $this->request = new Request();
    $this->session = new Session($this->config->Get('session.name'), $this->config->Get('session.time'));
    $this->i18n = new i18n();
  }
  
  public function Run(string $scope)
  {
    $this->scope = $scope;
  }
}