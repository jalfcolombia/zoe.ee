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
 * @category Session
 * @package  ZoeEE
 * @author   Julian Lasso <jalasso69@misena.edu.co>
 * @license  https://github.com/jalfcolombia/zoe.ee/blob/master/LICENSE Apache2
 * @link     https://github.com/jalfcolombia/zoe.ee
 */

namespace ZoeEE\Session;

/**
 * Clase para manejar las sesiones del sistema
 *
 * @category Session
 * @package  ZoeEE
 * @author   Julian Lasso <jalasso69@misena.edu.co>
 * @license  https://github.com/jalfcolombia/zoe.ee/blob/master/LICENSE Apache2
 * @link     https://github.com/jalfcolombia/zoe.ee
 */
class Redis extends \Redis
{

    private $redis;

    /**
     * Constructor de la clase Redis
     *
     * @param string $name Nombre de la cookie para manejar la sesión en el cliente
     * @param int    $time [opcional] Tiempo en segundo de duración de la sesión (3600 = 1 hora)
     */
    public function __construct(string $id_conection, string $host, int $port = 6379, $timeout = 0)
    {
        $this->redis = new \Redis();
        if ($this->redis->pconnect($host, $port, $timeout, $id_conection) === false) {
            throw new \RuntimeException('Parece ser que el servidor Redis no está en línea');
        }
    }

    public function getConnection(): \Redis
    {
        return $this->redis;
    }

}
