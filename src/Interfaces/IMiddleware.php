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