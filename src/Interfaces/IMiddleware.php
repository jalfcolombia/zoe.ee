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
 *
 * PHP version 7.2
 *
 * @category Interfaces
 * @package  ZoeEE
 * @author   Julian Lasso <jalasso69@misena.edu.co>
 * @license  https://github.com/jalfcolombia/zoe.ee/blob/master/LICENSE Apache2
 * @link     https://github.com/jalfcolombia/zoe.ee
 */

namespace ZoeEE\Interfaces;

use ZoeEE\i18n\i18n;
use ZoeEE\Config\Config;
use ZoeEE\Request\Request;
use ZoeEE\Routing\Routing;
use ZoeEE\Session\Session;

/**
 * Interface para los middlewares
 *
 * @category   Interfaces
 * @package    ZoeEE
 * @subpackage Middleware
 * @author     Julian Lasso <jalasso69@misena.edu.co>
 * @license    https://github.com/jalfcolombia/zoe.ee/blob/master/LICENSE Apache2
 * @link       https://github.com/jalfcolombia/zoe.ee
 */
interface IMiddleware
{

    /**
     * Clase principal de los middlewares
     *
     * @param Request $request Instancia de la clase Request del sistema
     * @param i18n    $i18n    Instancia de la clase i18n del sistema
     * @param Config  $config  Instancia de la clase Config del sistema
     * @param Session $session Instancia de la clase Session del sistema
     * @param Routing $routing Instancia de la clase Routing del sistema
     *
     * @return void
     */
    public function main(Request $request, i18n $i18n, Config $config, Session $session, Routing $routing);
}
