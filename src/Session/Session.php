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
class Session
{

    private const ID_CURRENT_USER = 'id_current_user';

    private $redis;
    private $name;
    private $id;
    private $time;

    /**
     * Constructor de la clase Session
     *
     * @param string $name Nombre de la cookie para manejar la sesión en el cliente
     * @param int    $time [opcional] Tiempo en segundo de duración de la sesión (3600 = 1 hora)
     */
    public function __construct(string $name, int $time = 3600)
    {
        $this->start($time);
        $this->setName($name);
    }

    public function getTime(int $time): int
    {
        return $this->time;
    }

    public function setTime(int $time): Session
    {
        $this->time = time() + $time;
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
        $this->setTime($time);
        $this->redis = new \Redis();
        if ($this->redis->pconnect('localhost', 6379) === false) {
            throw new \RuntimeException('Parece ser que el servidor Redis no está en línea');
        }
        return $this;
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
        $this->name = $name;
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

    public function hasId(): bool
    {
        return ($this->redis->exists(hash('crc32', $this->name)) === 1) ? true : false;
    }

    /**
     * Devuelve el ID de la sesión establecida
     *
     * @return string ID de la sesión establecida
     */
    public function getId(): string
    {
        return $this->id;
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
        $this->id = hash('crc32', $id);
        if ($this->hasId() === false) {
            $this->redis->hMSet($this->id, array('init' => true));
            $this->redis->expireAt($this->id, $this->time);
        }
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
        $GLOBALS["{$this->id}.$param"] = $value;
        $this->redis->hMSet($this->id, array($param => $value));
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
        return (isset($GLOBALS["{$this->id}.$param"]) === true) ? true : $this->redis->hExists($this->id, $param);
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
        if (isset($GLOBALS["{$this->id}.$param"]) === false) {
            $GLOBALS["{$this->id}.$param"] = $this->redis->hGet($this->id, $param);
        }
        return $GLOBALS["{$this->id}.$param"];
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
        unset($GLOBALS["{$this->id}.$param"]);
        $this->redis->hDel($this->id, $param);
        return $this;
    }

    /**
     * Destruye toda la información registrada de una sesión
     *
     * @return Session
     */
    public function destroy(): Session
    {
        $this->redis->unlink($this->id);
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
        if (isset($GLOBALS[self::ID_CURRENT_USER]) === true) {
            return $GLOBALS[self::ID_CURRENT_USER];
        } else {
            $session = new Session($GLOBALS['token']);
            $session->setId($GLOBALS['token']);
            return ($session->has(self::ID_CURRENT_USER)) ? $session->get(self::ID_CURRENT_USER) : null;
        }
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
        $GLOBALS[self::ID_CURRENT_USER] = $id;
        $this->set(self::ID_CURRENT_USER, $id);
        return $this;
    }

    public function generateSeedForToken(): string
    {
        return rand() . "-" . str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ$+.|@=*/^`'#~()?") . "-" . date('d-m-Y H:i:s') . "-" . time();
    }

}
