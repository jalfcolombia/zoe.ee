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

namespace ZoeEE\Session\Adapter;

/**
 * Clase para manejar las sesiones del sistema
 *
 * @category Session
 * @package  ZoeEE
 * @author   Julian Lasso <jalasso69@misena.edu.co>
 * @license  https://github.com/jalfcolombia/zoe.ee/blob/master/LICENSE Apache2
 * @link     https://github.com/jalfcolombia/zoe.ee
 */
class Redis
{
    private $redis;
    private $name;
    private $time;

    /**
     * Constructor de la clase Session
     *
     * @param string $name Nombre de la cookie para manejar la sesión en el cliente
     * @param int    $time [opcional] Tiempo en segundo de duración de la sesión (3600 = 1 hora)
     */
    public function __construct(string $name, int $time = 3600)
    {
        $this->setName($name);
        $this->time = time(NULL) + $time;
        $this->redis = new Redis();
        $this->redis->connect('localhost', 6379, 10);
    }

    /**
     * Establece el nombre de la cookie para manejar la sesión
     *
     * @param string   $name Nombre de la cookie
     *
     * @return Session Instancia de la clase Session
     */
    public function setName(string $name): Session
    {
        $this->name = hash('crc32', $this->name);
        return $this;
    }

    /**
     * Establece una variable de sesión
     *
     * @param string $param Nombre de la variable
     * @param mixed  $value Valor de la variable
     *
     * @return Session Instancia de la clase Session
     */
    public function set(string $param, $value): Session
    {
        $param = "{$this->name}:{$param}";
        $this->redis->set($param, $value);
        $this->redis->expireAt($param, $this->time);
        return $this;
    }

    /**
     * Evalua la existencia de una variable de sesión
     *
     * @param string $param Nombre de variable
     *
     * @return bool Falso si no existe de lo contrario Verdadero
     */
    public function has(string $param): bool
    {
        $param = "{$this->name}:{$param}";
        return ($this->redis->exists($param) == true) ? true : false;
    }

    /**
     * Devuelve el valor de una variable de sesión
     *
     * @param string $param Nombre de la variable
     *
     * @return mixed Valor de la variable de sesión
     */
    public function get(string $param)
    {
        $param = "{$this->name}:{$param}";
        return $this->redis->get($param);
    }

    /**
     * Borra una variable de sesión
     *
     * @param string $param Nombre de la variable
     *
     * @return Session Instancia de la clase Session
     */
    public function delete(string $param): Session
    {
        $param = "{$this->name}:{$param}";
        $this->redis->unlink($param);
        return $this;
    }

    /**
     * Devuelve el nombre de la cookie que maneja la sesión en el cliente
     *
     * @return string Nombre de la cookie
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Devuelve el ID de la sesión establecida
     *
     * @return string ID de la sesión establecida
     */
    public function getId(): string
    {
        return $this->name;
    }

    /**
     * Establece el ID a la sesión presente
     *
     * @param string $id ID para la sesión presente
     *
     * @return Session Instancia de la clase Session
     */
    public function setId(string $id): Session
    {
        $this->setName($id);
        return $this;
    }

    /**
     * Da comienzo a la sesión estableciendo el tiempo de expiración en segundos
     *
     * @param int $time Tiempo en segundos
     *
     * @return Session Instancia del objecto Session
     */
    public function start(int $time): Session
    {
        $this->time = time(NULL) + $time;
        $this->redis = new Redis();
        $this->redis->connect('localhost', 6379, 10);
        return $this;
    }

    /**
     * Destruye toda la información registrada de una sesión
     *
     * @return Session
     */
    public function destroy(): Session
    {
        $this->redis->flushDb();
        return $this;
    }

    /**
     * Obtiene el ID del usuario ya identificado en la sesión actual
     *
     * @param string $session_id Description
     *
     * @return int|null Número de identificación del usuario, en caso de no haber un ID devuelve NULL
     */
    public static function GetCurrentUser(): ?int
    {
        $session = new Session($GLOBALS['session_id']);
        $session->setId($session_id);
        return ($session->has('id_current_user')) ? $session->get('id_current_user') : null;
    }

    /**
     * Establece el id del usuario actual ya identificado
     *
     * @param int $id ID del usuario identificado en la sesión
     *
     * @return Session
     */
    public function setCurrentUser(int $id): Session
    {
        $this->set('id_current_user', $id);
        return $this;
    }
}
