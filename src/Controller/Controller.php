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
     * @param Request $request Objeto para el manejo de las solicitudes al sistema
     * @param i18n $i18n Objeto para manejar la internacionalización de los mensajes del sistema
     * @param Config $config Objeto para manejar la configuración del sistema
     * @param Session $session Objeto para manejar las sesiones del sistema
     * @param Routing $routing Objeto para manejar y controlar las rutas del sistema
     */
    abstract function main(Request $request, i18n $i18n, Config $config, Session $session, Routing $routing);

    /**
     * Constructor de todos los controladores del sistema
     *
     * @param Cache $cache Objeto para manejar la caché del sistema
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
     * @param string $view Nombre de la vista a usar
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
