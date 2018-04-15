<?php
namespace ZoeEE\Middleware\Interfaces;

use ZoeEE\Request\Request;
use ZoeEE\Config\Config;
use ZoeEE\Session\Session;
use ZoeEE\i18n\i18n;
use ZoeEE\Routing\Routing;

/**
 * Interface para los middlewares
 */
interface IntfcMiddleware
{

    public function Main(Request $request, i18n $i18n, Config $config, Session $session, Routing $routing);
}