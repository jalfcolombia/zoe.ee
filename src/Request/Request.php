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
 * 
 * @author Julian Lasso <jalasso69@misena.edu.co>
 * @package ZoeEE
 * @subpackage Request
 */
class Request
{

    private $get;

    private $post;

    private $put;

    private $delete;

    private $header;

    private $cookie;

    private $file;

    private $server;

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

    public function hasQuery(string $param): bool
    {
        return isset($this->get[$param]);
    }

    public function getQuery(string $param)
    {
        return $this->get[$param];
    }

    public function deleteQuery(string $param): Request
    {
        unset($this->get[$param]);
        return $this;
    }

    public function hasParam(string $param): bool
    {
        return isset($this->post[$param]);
    }

    public function getParam(string $param)
    {
        return $this->post[$param];
    }

    public function deleteParam(string $param): Request
    {
        unset($this->post[$param]);
        return $this;
    }

    public function hasPut(string $param): bool
    {
        return isset($this->put[$param]);
    }

    public function getPut(string $param)
    {
        return $this->put[$param];
    }

    public function deletePut(string $param): Request
    {
        unset($this->put[$param]);
        return $this;
    }

    public function hasDelete(string $param): bool
    {
        return isset($this->delete[$param]);
    }

    public function getDelete(string $param)
    {
        return $this->delete[$param];
    }

    public function deleteDelete(string $param): Request
    {
        unset($this->delete[$param]);
        return $this;
    }

    public function hasHeader(string $param): bool
    {
        return isset($this->header[$param]);
    }

    public function getHeader(string $param)
    {
        return $this->header[$param];
    }

    public function deleteHeader(string $param): Request
    {
        unset($this->header[$param]);
        return $this;
    }

    public function hasCookie(string $param): bool
    {
        return isset($this->cookie[$param]);
    }

    public function getCookie(string $param)
    {
        return $this->cookie[$param];
    }

    public function deleteCookie(string $param): Request
    {
        unset($this->cookie[$param]);
        return $this;
    }

    public function hasFile(string $file): bool
    {
        return isset($this->file[$file]);
    }

    public function getFile(string $file): ?string
    {
        return ($this->hasFile($file) === true) ? $this->file[$file] : null;
    }

    public function deleteFile(string $file): Request
    {
        unset($this->file[$file]);
        return $this;
    }

    public function hasServer(string $param): bool
    {
        return isset($this->server[$param]);
    }

    public function getServer(string $server): ?string
    {
        return ($this->hasServer($server) === true) ? $this->server[$server] : null;
    }
}
