<?php

namespace ZoeEE\Controller;

use ZoeEE\Cache\Cache;
use ZoeEE\Request\Request;
use ZoeEE\Config\Config;
use ZoeEE\Session\Session;
use ZoeEE\i18n\i18n;
use ZoeEE\Routing\Routing;

abstract class Controller
{

  /**
   * Objeto para manejar el caché del sistema
   * 
   * @var Cache
   */
  private $cache;
  private $view;

  abstract function Main(Request $request, i18n $i18n, Config $config, Session $session, Routing $routing);

  public function __construct(Cache $cache)
  {
    $this->cache = $cache;
    $this->view  = null;
  }

  /**
   * Devuelve el objeto para manejar el caché del sistema
   * 
   * @return Cache
   */
  protected function GetCache(): Cache
  {
    return $this->cache;
  }

  protected function ChangeView(string $view): Controller
  {
    $this->view = $view;
    return $this;
  }

  public function GetView(): ?string
  {
    return $this->view;
  }

}
