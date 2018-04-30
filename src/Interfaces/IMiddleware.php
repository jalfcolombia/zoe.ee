<?php

/**
 * This file is part of the ZoeEE package.
 *
 * (c) Julian Lasso <jalasso69@misena.edu.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZoeEE\Interfaces;

use ZoeEE\Config\Config;
use ZoeEE\Request\Request;
use ZoeEE\Routing\Routing;
use ZoeEE\Session\Session;
use ZoeEE\i18n\i18n;

/**
 * Interface para los middlewares
 *
 * @author Julian Lasso <jalasso69@misena.edu.co>
 * @package ZoeEE
 * @subpackage Middleware
 * @subpackage Interfaces
 */
interface IMiddleware
{

    /**
     * Clase principal de los middlewares
     *
     * @param Request $request
     * @param i18n $i18n
     * @param Config $config
     * @param Session $session
     * @param Routing $routing
     */
    public function main(Request $request, i18n $i18n, Config $config, Session $session, Routing $routing);
}