<?php
namespace ZoeEE\Request;

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
        $this->get = (filter_input_array(INPUT_GET) === false) ? filter_input_array(INPUT_GET) : array();
        $this->post = (filter_input_array(INPUT_POST) === false) ? filter_input_array(INPUT_POST) : array();
        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'PUT') {
            parse_str(file_get_contents("php://input"), $this->put);
            $this->delete = array();
        } else if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == 'DELETE') {
            parse_str(file_get_contents("php://input"), $this->delete);
            $this->put = array();
        }
        $this->header = (getallheaders() === false) ? array() : getallheaders();
        $this->cookie = (filter_input_array(INPUT_COOKIE) === false) ? filter_input_array(INPUT_COOKIE) : array();
        $this->file = $_FILES;
        $this->server = (filter_input_array(INPUT_SERVER) === false) ? filter_input_array(INPUT_SERVER) : array();
    }

    public function HasQuery(string $param): bool
    {
        return isset($this->get[$param]);
    }

    public function GetQuery(string $param)
    {
        return $this->get[$param];
    }

    public function DeleteQuery(string $param): Request
    {
        unset($this->get[$param]);
        return $this;
    }

    public function HasParam(string $param): bool
    {
        return isset($this->post[$param]);
    }

    public function GetParam(string $param)
    {
        return $this->post[$param];
    }

    public function DeleteParam(string $param): Request
    {
        unset($this->post[$param]);
        return $this;
    }

    public function HasPut(string $param): bool
    {
        return isset($this->put[$param]);
    }

    public function GetPut(string $param)
    {
        return $this->put[$param];
    }

    public function DeletePut(string $param): Request
    {
        unset($this->put[$param]);
        return $this;
    }

    public function HasDelete(string $param): bool
    {
        return isset($this->delete[$param]);
    }

    public function GetDelete(string $param)
    {
        return $this->delete[$param];
    }

    public function DeleteDelete(string $param): Request
    {
        unset($this->delete[$param]);
        return $this;
    }

    public function HasHeader(string $param): bool
    {
        return isset($this->header[$param]);
    }

    public function GetHeader(string $param)
    {
        return $this->header[$param];
    }

    public function DeleteHeader(string $param): Request
    {
        unset($this->header[$param]);
        return $this;
    }

    public function HasCookie(string $param): bool
    {
        return isset($this->cookie[$param]);
    }

    public function GetCookie(string $param)
    {
        return $this->cookie[$param];
    }

    public function DeleteCookie(string $param): Request
    {
        unset($this->cookie[$param]);
        return $this;
    }

    public function HasFile(string $file): bool
    {
        return isset($this->file[$file]);
    }

    public function GetFile(string $file)
    {
        return $this->cookie[$file];
    }

    public function DeleteFile(string $file): Request
    {
        unset($this->cookie[$file]);
        return $this;
    }

    public function HasServer(string $param): bool
    {
        return isset($this->server[$param]);
    }

    public function GetServer(string $server)
    {
        return $this->server[$server];
    }
}
