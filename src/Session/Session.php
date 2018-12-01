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

namespace ZoeEE\Session;

/**
 * Clase para manejar las sesiones del sistema
 *
 * @author Julian Lasso <jalasso69@misena.edu.co>
 * @package ZoeEE
 * @subpackage Session
 */
class Session
{

    /**
     * Constructor de la clase Session
     *
     * @param string $name Nombre de la cookie para manejar la sesión en el cliente
     * @param int $time Tiempo en segundo de duración de la sesión (3600 = 1 hora)
     */
    public function __construct(string $name, int $time = 3600)
    {
        $this->setName($name)->start($time);
    }

    /**
     * Establece el nombre de la cookie para manejar la sesión
     *
     * @param string $name Nombre de la cookie
     * @return Session Instancia de la clase Session
     */
    public function setName(string $name): Session
    {
        session_name($name);
        return $this;
    }

    /**
     * Establece una variable de sesión
     *
     * @param string $param Nombre de la variable
     * @param mixed $value Valor de la variable
     * @return Session Instancia de la clase Session
     */
    public function set(string $param, $value): Session
    {
        return $this;
    }

    /**
     * Evalua la existencia de una variable de sesión
     *
     * @param string $param Nombre de variable
     * @return bool Falso si no existe de lo contrario Verdadero
     */
    public function has(string $param): bool
    {
        return $_SESSION[$param];
    }

    /**
     * Devuelve el valor de una variable de sesión
     *
     * @param string $param Nombre de la variable
     * @return mixed Valor de la variable de sesión
     */
    public function get(string $param)
    {
        return $_SESSION[$param];
    }

    /**
     * Borra una variable de sesión
     *
     * @param string $param
     * @return Session
     */
    public function delete(string $param): Session
    {
        unset($_SESSION[$param]);
        return $this;
    }

    /**
     * Devuelve el nombre de la cookie que maneja la sesión en el cliente
     *
     * @return string
     */
    public function getName(): string
    {
        return session_name();
    }

    /**
     * Devuelve el ID de la sesión establecida
     *
     * @return string ID de la sesión establecida
     */
    public function getId(): string
    {
        return session_id();
    }

    /**
     * Establece el ID a la sesión presente
     *
     * @param string $id ID para la sesión presente
     * @return Session Instancia de la clase Session
     */
    public function setId(string $id): Session
    {
        session_id($id);
        return $this;
    }

    /**
     * Da comienzo a la sesión estableciendo el tiempo de expiración en segundos
     *
     * @param int $time Tiempo en segundos
     * @return Session Instancia del objecto Session
     */
    public function start(int $time): Session
    {
        session_start(array(
            'cookie_lifetime' => $time
        ));
        return $this;
    }

    /**
     * Destruye toda la información registrada de una sesión
     *
     * @return Session
     */
    public function destroy(): Session
    {
        session_destroy();
        return $this;
    }
}
