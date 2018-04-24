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
 * Clase abstracta para manejar los controladores del proyecto
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

    /**
     * Nombre de la vista a usar en la petición
     *
     * @var string|null
     */
    private $view;

    /**
     * Clase abstracta principal para todos los controladores
     *
     * @param Request $request
     *            Objeto para el manejo de las solicitudes al sistema
     * @param i18n $i18n
     *            Objeto para manejar la internacionalización de los mensajes del sistema
     * @param Config $config
     *            Objeto para manejar la configuración del sistema
     * @param Session $session
     *            Objeto para manejar las sesiones del sistema
     * @param Routing $routing
     *            Objeto para manejar y controlar las rutas del sistema
     */
    abstract function main(Request $request, i18n $i18n, Config $config, Session $session, Routing $routing);

    /**
     * Constructor de todos los controladores del sistema
     *
     * @param Cache $cache
     *            Objeto para manejar la caché del sistema
     */
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
     *            Nombre de la vista a usar
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
     * @return string|null
     */
    public function getView(): ?string
    {
        return $this->view;
    }
}
