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
use ZoeEE\Request\Request;
use ZoeEE\Config\Config;
use ZoeEE\Session\Session;
use ZoeEE\i18n\i18n;
use ZoeEE\Routing\Routing;

/**
 * 
 * @author Julian Lasso <jalasso69@misena.edu.co>
 * @package ZoeEE
 * @subpackage Controller
 */
abstract class Controller
{

    /**
     * Objeto para manejar el caché del sistema
     *
     * @var Cache
     */
    private $cache;

    private $view;

    abstract function main(Request $request, i18n $i18n, Config $config, Session $session, Routing $routing);

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
        $this->view = null;
    }

    /**
     * Devuelve el objeto para manejar el caché del sistema
     *
     * @return Cache
     */
    protected function getCache(): Cache
    {
        return $this->cache;
    }

    /**
     * Cambia la vista predeterminada en el routing
     *
     * @param string $view
     * @return Controller
     */
    protected function changeView(string $view): Controller
    {
        $this->view = $view;
        return $this;
    }

    /**
     * Devuelve la vista asignada desde el controlador
     *
     * @return string|NULL
     */
    public function getView(): ?string
    {
        return $this->view;
    }
}
