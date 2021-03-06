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
 * @category Request
 * @package  ZoeEE
 * @author   Julian Lasso <jalasso69@misena.edu.co>
 * @license  https://github.com/jalfcolombia/zoe.ee/blob/master/LICENSE Apache2
 * @link     https://github.com/jalfcolombia/zoe.ee
 */

namespace ZoeEE\Request;

/**
 * Clase para manejar las peticiones al sistema
 *
 * @category Request
 * @package  ZoeEE
 * @author   Julian Lasso <jalasso69@misena.edu.co>
 * @license  https://github.com/jalfcolombia/zoe.ee/blob/master/LICENSE Apache2
 * @link     https://github.com/jalfcolombia/zoe.ee
 */
class Request
{

    /**
     * Arreglo contenedor de las variables procedentes del método GET
     *
     * @var array
     */
    private $get;

    /**
     * Arreglo contenedor de las variables procedentes del método POST
     *
     * @var array
     */
    private $post;

    /**
     * Arreglo contenedor de las variables procedentes del método PUT
     *
     * @var array
     */
    private $put;

    /**
     * Arreglo contenedor de las variables procedentes del método DELETE
     *
     * @var array
     */
    private $delete;

    /**
     * Arreglo contenedor de los datos de la cabecera en la petición
     *
     * @var array
     */
    private $header;

    /**
     * Arreglo contenedor de las cookies
     *
     * @var array
     */
    private $cookie;

    /**
     * Arreglo contenedor de los archivos precedentes en la petición al sistema
     *
     * @var array
     */
    private $file;

    /**
     * Arreglo contenedor de las variables del servidor en la petición al sistema
     *
     * @var array
     */
    private $server;

    /**
     * Constructor de la clase Request
     */
    public function __construct()
    {
        $this->get    = (filter_input_array(INPUT_GET) === false) ? array() : filter_input_array(INPUT_GET);
        $this->post   = (filter_input_array(INPUT_POST) === false) ? array() : filter_input_array(INPUT_POST);
        $this->delete = array();
        $this->put    = array();
        $this->header = (getallheaders() === false) ? array() : getallheaders();
        $this->cookie = (filter_input_array(INPUT_COOKIE) === false) ? array() : filter_input_array(INPUT_COOKIE);
        $this->file   = $_FILES;
        $this->server = (filter_input_array(INPUT_SERVER) === false) ? array() : filter_input_array(INPUT_SERVER);

        $type_request = strtolower(filter_input(INPUT_SERVER, 'REQUEST_METHOD'));
        $request_body = file_get_contents('php://input');
        if ($this->isJson($request_body) === true) {
            // echo 1;
            $this->{$type_request} = array_merge((array) $this->{$type_request}, (array) json_decode($request_body));
        } elseif (strlen($type_request) > 0) {
            // echo 2;
            parse_str($request_body, $tmp_data);
            if ($type_request === 'delete') {
                $this->{$type_request} = array_merge((array) $this->{$type_request}, $this->get, (array) $tmp_data);
            } else {
                $this->{$type_request} = array_merge((array) $this->{$type_request}, (array) $tmp_data);
            }
        }

        // print_r($this->{$type_request}); exit();
    }

    /**
     * Evalua si la petición fue realizada vía AJAX
     *
     * @return bool TRUE si la petición es realizada vía AJAX, de otro modo devuelve FALSE
     */
    public function isAjax(): bool
    {
        if (isset($this->server['HTTP_X_REQUESTED_WITH']) === true and
                strtolower($this->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Evalua la existencia de una parámetro procedente del método GET
     *
     * @param string $param Nombre de la variable
     *
     * @return bool Falso si no existe de lo contrario Verdadero
     */
    public function hasQuery(string $param): bool
    {
        return isset($this->get[$param]);
    }

    /**
     * Obtiene el valor de un parámetro procedente del método GET
     *
     * @param string $param Nombre del parámetro
     *
     * @return mixed Valor del parámetro
     */
    public function getQuery(string $param)
    {
        return ($this->hasQuery($param)) ? $this->get[$param] : null;
    }

    /**
     * Borra un parámetro procediente del método GET
     *
     * @param string $param Nombre del parámetro
     *
     * @return Request Instancia del objeto Request
     */
    public function deleteQuery(string $param): Request
    {
        unset($this->get[$param]);
        return $this;
    }

    /**
     * Evalua la existencia de una parámetro procedente del método POST
     *
     * @param string $param Nombre del parámetro
     *
     * @return bool Falso si no existe de lo contrario Verdadero
     */
    public function hasParam(string $param): bool
    {
        return isset($this->post[$param]);
    }

    /**
     * Obtiene el valor de un parámetro procedente del método POST
     *
     * @param string $param Nombre del parámetro
     *
     * @return mixed Valor del parámetro
     */
    public function getParam(string $param)
    {
        return ($this->hasParam($param)) ? $this->post[$param] : null;
    }

    /**
     * Borra un parámetro procediente del método POST
     *
     * @param string $param Nombre del parámetro
     *
     * @return Request Instancia del objeto Request
     */
    public function deleteParam(string $param): Request
    {
        unset($this->post[$param]);
        return $this;
    }

    /**
     * Evalua la existencia de una parámetro procedente del método PUT
     *
     * @param string $param Nombre del parámetro
     *
     * @return bool Falso si no existe de lo contrario Verdadero
     */
    public function hasPut(string $param): bool
    {
        return isset($this->put[$param]);
    }

    /**
     * Obtiene el valor de un parámetro procedente del método PUT
     *
     * @param string $param Nombre del parámetro
     *
     * @return string|null Valor del parámetro
     */
    public function getPut(string $param): ?string
    {
        return ($this->hasPut($param)) ? $this->put[$param] : null;
    }

    /**
     * Borra un parámetro procediente del método PUT
     *
     * @param string $param Nombre del parámetro
     *
     * @return Request Instancia del objeto Request
     */
    public function deletePut(string $param): Request
    {
        unset($this->put[$param]);
        return $this;
    }

    /**
     * Evalua la existencia de una parámetro procedente del método DELETE
     *
     * @param string $param Nombre del parámetro
     *
     * @return bool Falso si no existe de lo contrario Verdadero
     */
    public function hasDelete(string $param): bool
    {
        return isset($this->delete[$param]);
    }

    /**
     * Obtiene el valor de un parámetro procedente del método DELETE
     *
     * @param string $param Nombre del parámetro
     *
     * @return string Valor del parámetro
     */
    public function getDelete(string $param): ?string
    {
        return ($this->hasDelete($param)) ? $this->delete[$param] : null;
    }

    /**
     * Borra un parámetro procediente del método DELETE
     *
     * @param string $param Nombre del parámetro
     *
     * @return Request Instancia del objeto Request
     */
    public function deleteDelete(string $param): Request
    {
        unset($this->delete[$param]);
        return $this;
    }

    /**
     * Evalua la existencia de una parámetro procedente de las cabeceras de la
     * petición al sistema
     *
     * @param string $param Nombre del parámetro
     *
     * @return bool Falso si no existe de lo contrario Verdadero
     */
    public function hasHeader(string $param): bool
    {
        return isset($this->header[$param]);
    }

    /**
     * Obtiene el valor de un parámetro procedente de las cabeceras de la
     * petición al sistema
     *
     * @param string $param Nombre del parámetro
     *
     * @return string Valor del parámetro
     */
    public function getHeader(string $param): ?string
    {
        return ($this->hasHeader($param)) ? $this->header[$param] : null;
    }

    /**
     * Borra un parámetro procediente de las cabeceras de la petición al sistema
     *
     * @param string $param Nombre del parámetro
     *
     * @return Request Instancia del objeto Request
     */
    public function deleteHeader(string $param): Request
    {
        unset($this->header[$param]);
        return $this;
    }

    /**
     * Evalua la existencia de una cookie procedente de la petición al sistema
     *
     * @param string $param Nombre de la cookie
     *
     * @return bool Falso si no existe de lo contrario Verdadero
     */
    public function hasCookie(string $param): bool
    {
        return isset($this->cookie[$param]);
    }

    /**
     * Obtiene el valor de una cookie procedente de la petición al sistema
     *
     * @param string $param Nombre de la cookie
     *
     * @return string Valor de la cookie
     */
    public function getCookie(string $param): ?string
    {
        return ($this->hasCookie($param)) ? $this->cookie[$param] : null;
    }

    /**
     * Borra una cookie procedente de la petición al sistema
     *
     * @param string $param Nombre de la cookie
     *
     * @return Request Instancia del objeto Request
     */
    public function deleteCookie(string $param): Request
    {
        unset($this->cookie[$param]);
        return $this;
    }

    /**
     * Evalua la existencia de un archivo procedente de la petición al sistema
     *
     * @param string $param Nombre del archivo
     *
     * @return bool Falso si no existe de lo contrario Verdadero
     */
    public function hasFile(string $file): bool
    {
        return isset($this->file[$file]);
    }

    /**
     * Obtiene el arreglo con los datos del archivo procedente de la
     * petición al sistema
     *
     * @param string $param Nombre del archivo
     *
     * @return array|null Arreglo con información del archivo
     */
    public function getFile(string $file): ?array
    {
        return ($this->hasFile($file) === true) ? $this->file[$file] : null;
    }

    /**
     * Obtiene el arreglo de los archivos que procden de la
     * petición al sistema
     *
     * @return array Arreglo con información de los archivos cargados al servidor
     */
    public function getFiles(): array
    {
        return $this->file;
    }

    /**
     * Borra un archivo procedente de la petición al sistema
     *
     * @param string $param Nombre del archivo
     *
     * @return Request Instancia del objeto Request
     */
    public function deleteFile(string $file): Request
    {
        unset($this->file[$file]);
        return $this;
    }

    /**
     * Evalua la existencia de una variable procedente de las variables
     * del servidor resultantes de la petición al sistema
     *
     * @param string $param Nombre del parámetro
     *
     * @return bool Falso si no existe de lo contrario Verdadero
     */
    public function hasServer(string $param): bool
    {
        return isset($this->server[$param]);
    }

    /**
     * Obtiene el valor de una variable del servidor resultante de la
     * petición al sistema
     *
     * @param string $param Nombre del parámetro
     *
     * @return string Valor del parámetro
     */
    public function getServer(string $server): ?string
    {
        return ($this->hasServer($server)) ? $this->server[$server] : null;
    }

    private function isJson($string): bool
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

}
