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
 * @category Routing
 * @package  ZoeEE
 * @author   Julian Lasso <jalasso69@misena.edu.co>
 * @license  https://github.com/jalfcolombia/zoe.ee/blob/master/LICENSE Apache2
 * @link     https://github.com/jalfcolombia/zoe.ee
 */

namespace ZoeEE\Routing;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use ZoeEE\Cache\Cache;
use ZoeEE\Controller\Controller;
use ZoeEE\Controller\FrontController;
use ZoeEE\ExceptionHandler\ZOEException;

 /**
 * Clase para controlar el sistema de rutas basado en un arhchivo YAML
 *
 * @category Routing
 * @package  ZoeEE
 * @author   Julian Lasso <jalasso69@misena.edu.co>
 * @license  https://github.com/jalfcolombia/zoe.ee/blob/master/LICENSE Apache2
 * @link     https://github.com/jalfcolombia/zoe.ee
 */
class Routing
{

    /**
     * Nombre de la variable en caché de la memoria RAM
     */
    private const NAME_CACHE = 'zoeRouting';

    /**
     * Dirección y nombre de archivo en la caché
     */
    private const CACHE = 'Confing' . DIRECTORY_SEPARATOR . 'Routing';

    /**
     * Dirección y nombre del archivo YAML
     */
    private const YAML = 'Config' . DIRECTORY_SEPARATOR . 'Routing.yml';

    /**
     * Nombre del folder donde se encuentran los paquetes del sistema
     */
    private const BUNDLE = 'Bundle' . DIRECTORY_SEPARATOR;

    /**
     * Campo de aplicación de desarrollo
     */
    private const DEV = 'dev';

    /**
     * Campo de aplicación de producción
     */
    private const PROD = 'prod';

    /**
     * Campo de aplicación de testeo
     */
    private const TEST = 'test';

    /**
     * Objeto para manejar el caché del sistema
     *
     * @var Cache
     */
    private $cache;

    /**
     * Ruta a la que intentan acceder
     *
     * @var string
     */
    private $path;

    /**
     * Arreglo con los datos de la ruta solicitada
     *
     * @var array
     */
    private $route;

    /**
     * Arreglo asociativo con los parámetos pasados por la URL
     *
     * @var array
     */
    private $params;

    /**
     * Ruta de donde se encuentra el proyecto físico en el servidor
     *
     * @var string
     */
    private $path_project;

    /**
     * Método por el cual se le hace la petición al servidor
     * (GET, POST, PUT, DELETE)
     *
     * @var string
     */
    private $method;

    /**
     * Variable la cual me certifica si la petición al servidor fue
     * realizada vía AJAX (TRUE o FALSE)
     *
     * @var bool
     */
    private $is_ajax;

    /**
     * Campo de aplicación del sistema.
     * Ej: dev, prod o test
     *
     * @var string
     */
    private $scope;

    /**
     * Contiene un valor booleano para saber si la URL es válida o no
     *
     * @var bool
     */
    private $is_valid;

    /**
     * Constructor de la clase Routing
     *
     * @param string|null $path         Ruta a la que se intenta acceder (URL)
     * @param Cache       $cache        Objeto para manejar los archivos de la caché
     * @param string      $path_project Ruta del proyecto físico en el servidor
     * @param string      $method       Método por el cual llegan las peticiones (GET, POST, PUT, DELETE)
     * @param bool        $is_ajax      FALSE o TRUE dependiendo si la solicitud es vía AJAX o no
     * @param string      $scope        Campo de aplicación del sistema (dev, prod o test)
     */
    public function __construct(
        ?string $path,
        Cache $cache,
        string $path_project,
        string $method,
        bool $is_ajax,
        string $scope = FrontController::DEV
    ) {
        $this->params = array();
        $this->route = array();
        $this->path = ($path === null) ? '/' : $path;
        $this->cache = $cache;
        $this->path_project = $path_project;
        $this->method = $method;
        $this->is_ajax = $is_ajax;
        $this->scope = $scope;
        $this->is_valid = $this->solvePath();
    }

    /**
     * Remplaza la ultima aparición en una cadena de texto
     *
     * @param string $search  Cadena buscada
     * @param string $replace Cadena por la cual se reemplazará
     * @param string $string  Cadena en la que se debe buscar
     *
     * @return string Cadena resultante
     */
    private function strReplaceLast(string $search, string $replace, string $string): string
    {
        $pos = strrpos($string, $search);
        if ($pos !== false) {
            $string = substr_replace($string, $replace, $pos, strlen($search));
        }
        return $string;
    }

    /**
     * Resuelve la ruta provista
     *
     * @throws ZOEException
     *
     * @return bool Verdadero si encuentra una ruta en caso contrario devolverá Falso
     */
    private function solvePath(): bool
    {
        $routings = $this->getRoutingFile();
        foreach ($routings as $routing => $detail) {
            $method = true;
            if (isset($detail['method']) === true and
                is_array($detail['method']) === true and count($detail['method']) > 0
            ) {
                $detail['method'] = array_map('strtoupper', $detail['method']);
                $method = (array_search($this->method, $detail['method']) === false) ? false : true;
            } elseif (isset($detail['method']) === true and is_string($detail['method']) === true) {
                $method = (strtoupper($this->method) === strtoupper($detail['method'])) ? true : false;
            }

            $ajax = true;
            if (isset($detail['ajax']) === true and $this->is_ajax !== $detail['ajax']) {
                $ajax = false;
            }

            if ($method === true and $ajax === true) {
                $originalPath = $this->path;
                $yamlPath = $detail['path'];
                if (strpos($yamlPath, ':') !== false) {
                    $arrayPath = $arrayRoute = array();
                    if (strrpos($yamlPath, '.') !== false) {
                        $yamlPath = $this->strReplaceLast('.', '|', $yamlPath);
                        $originalPath = $this->strReplaceLast('.', '|', $originalPath);
                        $arrayRoute = explode('|', $yamlPath);
                        $arrayPath = explode('|', $originalPath);
                        $yamlPath = substr($yamlPath, 0, strrpos($yamlPath, '|'));
                        $originalPath = substr($this->path, 0, strrpos($originalPath, '|'));
                        unset($arrayPath[0], $arrayRoute[0]);
                    }
                    $arrayRoute = array_merge(explode('/', $yamlPath), $arrayRoute);
                    $arrayPath = array_merge(explode('/', $originalPath), $arrayPath);
                    unset($arrayPath[0], $arrayRoute[0]);
                    $cnt = count($arrayPath);
                    $cntTmp = 0;
                    if ($cnt === count($arrayRoute)) {
                        foreach ($arrayRoute as $key => $value) {
                            switch (strpos($value, ':')) {
                                // no encontró los dos puntos :
                                case false:
                                    if ($value === $arrayPath[$key]) {
                                        $cntTmp++;
                                    }
                                    break;
                                // si encontró con los dos puntos
                                default:
                                    $explode = explode(':', str_replace(array('{', '}'), '', $value));
                                    switch ($explode[0]) {
                                        case 'bool':
                                            if ($arrayPath[$key] === 'false') {
                                                $this->params[$explode[1]] = false;
                                                $cntTmp++;
                                            } elseif ($arrayPath[$key] === 'true') {
                                                $this->params[$explode[1]] = true;
                                                $cntTmp++;
                                            }
                                            break;
                                        case 'float':
                                            if (preg_match('/^[-]?([0-9]+).([0-9]+)$/', $arrayPath[$key])) {
                                                $this->params[$explode[1]] = (float)$arrayPath[$key];
                                                $cntTmp++;
                                            }
                                            break;
                                        case 'int':
                                            if (preg_match('/^[-]?([0-9]+)$/', $arrayPath[$key])) {
                                                $this->params[$explode[1]] = (int)$arrayPath[$key];
                                                $cntTmp++;
                                            }
                                            break;
                                        case 'integer':
                                            if (preg_match('/^[-]?([0-9]+)$/', $arrayPath[$key])) {
                                                $this->params[$explode[1]] = (integer)$arrayPath[$key];
                                                $cntTmp++;
                                            }
                                            break;
                                        case 'string':
                                            if (preg_match('/^([a-zA-z])+$/', $arrayPath[$key])) {
                                                $this->params[$explode[1]] = (string)$arrayPath[$key];
                                                $cntTmp++;
                                            }
                                            break;
                                        default:
                                            throw new ZOEException(sprintf(ZOEException::F0002, $explode[0]), 'F0002');
                                    }
                            }
                        }
                        if ($cnt === $cntTmp) {
                            $this->route = $detail;
                            return true;
                        }
                    }
                } elseif ($yamlPath === $originalPath) {
                    $this->route = $detail;
                    return true;
                }
            }
        }

        if (isset($routings['otherwise']) === true) {
            $this->route = $routings['otherwise'];
        } elseif (isset($routings['404']) === true) {
            $this->route = $routings['404'];
        } else {
            $this->route = $routings['index'];
        }
        return false;
    }

    /**
     * Devuelve el objeto del controlador asignado en la ruta
     *
     * @return Controller Instancia del controlador
     */
    public function getController(): Controller
    {
        $controller = null;
        if ($this->getProject() !== null) {
            eval("\$controller = new \\Bundle\\{$this->route['project']}\\Bundle\\{$this->route['bundle']}\\" .
            "Controller\\{$this->route['controller']}Controller(\$this->cache);");
        } else {
            eval("\$controller = new \\Bundle\\{$this->route['bundle']}\\Controller\\" .
            "{$this->route['controller']}Controller(\$this->cache);");
        }
        return $controller;
    }

    /**
     * Devuelve el nombre del paquete
     *
     * @return string Nombre del paquete
     */
    public function getBundle(): string
    {
        return $this->route['bundle'];
    }

    public function getProject(): ?string
    {
        return (isset($this->route['project']) === true ? $this->route['project'] : null);
    }

    public function getAction(): ?string
    {
        return (isset($this->route['action']) === true ? $this->route['action'] : null);
    }

    /**
     * Devuelve vista asignada en la ruta
     *
     * @return string Nombre de la vista
     */
    public function getView(): string
    {
        if (is_array($this->route['view']) === true) {
            return $this->route['view']['template'];
        } else {
            return $this->route['view'];
        }
    }

    /**
     * Devuelve los parámetros asignados en la ruta
     *
     * @return array Arreglo con los parámetros por el método GET en la ruta
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Devuelve falso o verdadero si encontró o no una ruta válida
     *
     * @return bool Falso si no encuentra una ruta válida o Verdadero en caso contrario
     */
    public function isValid(): bool
    {
        return $this->is_valid;
    }

    /**
     * Devuelve un arreglo con los parámetros de la ruta establecida
     *
     * @return array Arreglo de la ruta consultada
     */
    public function getRoute(): array
    {
        return $this->route;
    }

    /**
     * Devuelve un arreglo con las clases a ejecutar antes de ejecutar el
     * controlador principal solicitado
     *
     * @return array Arreglo de controladores
     */
    public function getMiddlewareBefore(): array
    {
        return $this->route['middleware']['before'];
    }

    /**
     * Devuelve un arreglo con las clases a ejecutar despues de ejecutar el
     * controlador principal solicitado
     *
     * @return array Arreglo de controladores
     */
    public function getMiddlewareAfter(): array
    {
        return $this->route['middleware']['after'];
    }

    /**
     * Devuelve un arreglo con las rutas del sistema
     *
     * @throws ZOEException
     *
     * @return array Arreglo de las rutas del sistema
     */
    protected function getRoutingFile(): array
    {
        try {
            if ($this->scope === self::DEV) {
                return $this->searchAllFilesYaml(
                    $this->path_project . self::BUNDLE .
                    (($this->getProject() !== null) ? $this->route['project'] . DIRECTORY_SEPARATOR : ''),
                    Yaml::parseFile($this->path_project . self::YAML)
                );
            } else {
                if (apcu_exists(self::NAME_CACHE) === true) {
                    return apcu_fetch(self::NAME_CACHE);
                } elseif ($this->cache->has(self::CACHE) === true) {
                    apcu_add(self::NAME_CACHE, (array)json_decode($this->cache->get(self::CACHE), true));
                    return apcu_fetch(self::NAME_CACHE);
                } else {
                    apcu_add(
                        self::NAME_CACHE,
                        $this->searchAllFilesYaml(
                            $this->path_project . self::BUNDLE .
                            (($this->getProject() !== null) ? $this->route['project'] . DIRECTORY_SEPARATOR : ''),
                            Yaml::parseFile($this->path_project . self::YAML)
                        )
                    );
                    $this->cache->set(self::CACHE, json_encode(apcu_fetch(self::NAME_CACHE), true));
                    return apcu_fetch(self::NAME_CACHE);
                }
            }
        } catch (ParseException $exc) {
            throw new ZOEException($exc->getMessage());
        }
    }

    /**
     * Busca en los paquetes del sistema el archivo Config/Routing.yml
     * para devolver un arreglo con las rutas del sistema
     *
     * @param string $path       Ruta del paquete
     * @param array  $routinInit Arreglo con los datos iniciales de las rutas
     *
     * @return array Arreglo con las rutas del sistema incluidas las de los paquétes
     */
    protected function searchAllFilesYaml(string $path, array $routinInit): array
    {
        if (is_dir($path) === true) {
            $dir = opendir($path);
            $yaml = $routinInit;
            while ($file = readdir($dir)) {
                // Busqueda en Bundles
                if ($file !== '.' and $file !== '..' and
                    is_file($path . $file . DIRECTORY_SEPARATOR . self::YAML) === true
                ) {
                    $yaml = array_merge($yaml, Yaml::parseFile($path . $file . DIRECTORY_SEPARATOR . self::YAML));
                }
                // Busqueda en Bundles de Proyectos
                if ($file !== '.' and $file !== '..' and
                    is_dir($path . $file . DIRECTORY_SEPARATOR . self::BUNDLE) === true
                ) {
                    $yaml = $this->searchAllFilesYaml($path . $file . DIRECTORY_SEPARATOR . self::BUNDLE, $yaml);
                }
            }
            return $yaml;
        }
    }
}
