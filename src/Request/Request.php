<?php

/**
 * This file is part of the ZoeEE package.
 *
 * (c) Julian Lasso <jalasso69@misena.edu.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZoeEE\Request;

/**
 * Clase para manejar las peticiones al sistema
 *
 * @author Julian Lasso <jalasso69@misena.edu.co>
 * @package ZoeEE
 * @subpackage Request
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
        $this->get = (filter_input_array(INPUT_GET) === false) ? array() : filter_input_array(INPUT_GET);
        $this->post = (filter_input_array(INPUT_POST) === false) ? array() : filter_input_array(INPUT_POST);
        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'PUT') {
            parse_str(file_get_contents("php://input"), $this->put);
            $this->delete = array();
        } else if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == 'DELETE') {
            parse_str(file_get_contents("php://input"), $this->delete);
            $this->put = array();
        }
        $this->header = (getallheaders() === false) ? array() : getallheaders();
        $this->cookie = (filter_input_array(INPUT_COOKIE) === false) ? array() : filter_input_array(INPUT_COOKIE);
        $this->file = $_FILES;
        $this->server = (filter_input_array(INPUT_SERVER) === false) ? array() : filter_input_array(INPUT_SERVER);
    }

    /**
     * Evalua la existencia de una parámetro procedente del método GET
     *
     * @param string $param
     *            Nombre de la variable
     * @return bool Falso si no existe de lo contrario Verdadero
     */
    public function hasQuery(string $param): bool
    {
        return isset($this->get[$param]);
    }

    /**
     * Obtiene el valor de un parámetro procedente del método GET
     *
     * @param string $param
     *            Nombre del parámetro
     * @return string Valor del parámetro
     */
    public function getQuery(string $param): string
    {
        return $this->get[$param];
    }

    /**
     * Borra un parámetro procediente del método GET
     *
     * @param string $param
     *            Nombre del parámetro
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
     * @param string $param
     *            Nombre del parámetro
     * @return bool Falso si no existe de lo contrario Verdadero
     */
    public function hasParam(string $param): bool
    {
        return isset($this->post[$param]);
    }

    /**
     * Obtiene el valor de un parámetro procedente del método POST
     *
     * @param string $param
     *            Nombre del parámetro
     * @return string Valor del parámetro
     */
    public function getParam(string $param): string
    {
        return $this->post[$param];
    }

    /**
     * Borra un parámetro procediente del método POST
     *
     * @param string $param
     *            Nombre del parámetro
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
     * @param string $param
     *            Nombre del parámetro
     * @return bool Falso si no existe de lo contrario Verdadero
     */
    public function hasPut(string $param): bool
    {
        return isset($this->put[$param]);
    }

    /**
     * Obtiene el valor de un parámetro procedente del método PUT
     *
     * @param string $param
     *            Nombre del parámetro
     * @return string Valor del parámetro
     */
    public function getPut(string $param): string
    {
        return $this->put[$param];
    }

    /**
     * Borra un parámetro procediente del método PUT
     *
     * @param string $param
     *            Nombre del parámetro
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
     * @param string $param
     *            Nombre del parámetro
     * @return bool Falso si no existe de lo contrario Verdadero
     */
    public function hasDelete(string $param): bool
    {
        return isset($this->delete[$param]);
    }

    /**
     * Obtiene el valor de un parámetro procedente del método DELETE
     *
     * @param string $param
     *            Nombre del parámetro
     * @return string Valor del parámetro
     */
    public function getDelete(string $param): string
    {
        return $this->delete[$param];
    }

    /**
     * Borra un parámetro procediente del método DELETE
     *
     * @param string $param
     *            Nombre del parámetro
     * @return Request Instancia del objeto Request
     */
    public function deleteDelete(string $param): Request
    {
        unset($this->delete[$param]);
        return $this;
    }

    /**
     * Evalua la existencia de una parámetro procedente de las cabeceras de la petición al sistema
     *
     * @param string $param
     *            Nombre del parámetro
     * @return bool Falso si no existe de lo contrario Verdadero
     */
    public function hasHeader(string $param): bool
    {
        return isset($this->header[$param]);
    }

    /**
     * Obtiene el valor de un parámetro procedente de las cabeceras de la petición al sistema
     *
     * @param string $param
     *            Nombre del parámetro
     * @return string Valor del parámetro
     */
    public function getHeader(string $param): string
    {
        return $this->header[$param];
    }

    /**
     * Borra un parámetro procediente de las cabeceras de la petición al sistema
     *
     * @param string $param
     *            Nombre del parámetro
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
     * @param string $param
     *            Nombre de la cookie
     * @return bool Falso si no existe de lo contrario Verdadero
     */
    public function hasCookie(string $param): bool
    {
        return isset($this->cookie[$param]);
    }

    /**
     * Obtiene el valor de una cookie procedente de la petición al sistema
     *
     * @param string $param
     *            Nombre de la cookie
     * @return string Valor de la cookie
     */
    public function getCookie(string $param): string
    {
        return $this->cookie[$param];
    }

    /**
     * Borra una cookie procedente de la petición al sistema
     *
     * @param string $param
     *            Nombre de la cookie
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
     * @param string $param
     *            Nombre del archivo
     * @return bool Falso si no existe de lo contrario Verdadero
     */
    public function hasFile(string $file): bool
    {
        return isset($this->file[$file]);
    }

    /**
     * Obtiene el arreglo con los datos del archivo procedente de la petición al sistema
     *
     * @param string $param
     *            Nombre del archivo
     * @return array|null Arreglo con información del archivo
     */
    public function getFile(string $file): ?array
    {
        return ($this->hasFile($file) === true) ? $this->file[$file] : null;
    }

    /**
     * Borra un archivo procedente de la petición al sistema
     *
     * @param string $param
     *            Nombre del archivo
     * @return Request Instancia del objeto Request
     */
    public function deleteFile(string $file): Request
    {
        unset($this->file[$file]);
        return $this;
    }

    /**
     * Evalua la existencia de una variable procedente de las variables del servidor resultantes de la petición al sistema
     *
     * @param string $param
     *            Nombre del parámetro
     * @return bool Falso si no existe de lo contrario Verdadero
     */
    public function hasServer(string $param): bool
    {
        return isset($this->server[$param]);
    }

    /**
     * Obtiene el valor de una variable del servidor resultante de la petición al sistema
     *
     * @param string $param
     *            Nombre del parámetro
     * @return string Valor del parámetro
     */
    public function getServer(string $server): ?string
    {
        return ($this->hasServer($server) === true) ? $this->server[$server] : null;
    }
}
