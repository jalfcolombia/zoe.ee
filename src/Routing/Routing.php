<?php

namespace ZoeEE\Routing;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use ZoeEE\Cache\Cache;
use ZoeEE\Controller\Controller;
use ZoeEE\ExceptionHandler\ZOEException;

/**
 * Clase para controlar el sistema de rutas basado en un arhchivo YAML
 * 
 * 
 * @author Julian Andres Lasso Figueroa <jalasso69@misena.edu.co>
 * @package ZoEE\Routing
 */
class Routing
{

    private const NAME_CACHE = 'zoeRouting';

    /**
     * Dirección y nombre de archivo en la cachÃ©
     */
    private const CACHE = 'Confing' . DIRECTORY_SEPARATOR . 'Routing';

    /**
     * Dirección y nombre del archivo YAML
     */
    private const YAML = 'Config' . DIRECTORY_SEPARATOR . 'Routing.yml';

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
     * Objeto para manejar el cachÃ© del sistema
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
    public $route;

    /**
     * Arreglo asociativo con los parámetos pasados por la URL
     *
     * @var array
     */
    private $params;

    /**
     * Ruta de donde se encuentra el folder de configuración
     *
     * @var string
     */
    private $path_proyect;

    /**
     * Campo de aplicación del sistema.
     * Ej: dev, prod o test
     *
     * @var string
     */
    private $scope;

    /**
     * Contiene un valor buleano para saber si la URL es válida o no
     *
     * @var bool
     */
    private $is_valid;

    /**
     * Constructor de la clase Routing
     * 
     * @param string $path Ruta a la que se intenta acceder
     * @param Cache $cache Objeto para manejar los archivos de la caché
     * @param string $path_proyect Ruta del proyecto físico en el servidor
     * @param string $scope Campo de aplicación del sistema (dev, prod o test)
     */
    public function __construct(string $path, Cache $cache, string $path_proyect, string $scope = self::DEV)
    {
        $this->params = array();
        $this->route = array();
        $this->path = $path;
        $this->cache = $cache;
        $this->path_proyect = $path_proyect;
        $this->scope = $scope;
        $this->is_valid = $this->solvePath();
    }

    /**
     * Resuelve la ruta provista
     * 
     * @throws ZOEException
     * @return bool Verdadero si encontró una ruta en caso contrario devolverá Falso
     */
    private function solvePath(): bool
    {
        $routings = $this->getRoutingFile();
        foreach ($routings as $routing => $detail) {
            if (strpos($detail['path'], ':') !== false) {
                
                $arrayPath = $arrayRoute = array();
                if (strpos($detail['path'], '.') !== false) {
                    $arrayRoute = explode('.', $detail['path']);
                    $arrayPath = explode('.', $this->path);
                    $detail['path'] = substr($detail['path'], 0, strrpos($detail['path'], '.'));
                    $this->path = substr($this->path, 0, strrpos($this->path, '.'));
                    unset($arrayPath[0], $arrayRoute[0]);
                }

                $arrayRoute = array_merge(explode('/', $detail['path']), $arrayRoute);
                $arrayPath = array_merge(explode('/', $this->path), $arrayPath);
                unset($arrayPath[0], $arrayRoute[0]);

                $cnt = count($arrayPath);
                $cntTmp = 0;
                if ($cnt === count($arrayRoute)) {
                    
                    foreach ($arrayRoute as $key => $value) {
                        switch (strpos($value, ':')) {
                            // no encontró los dos puntos :
                            case false:
                                if ($value === $arrayPath[$key]) {
                                    $cntTmp ++;
                                }
                                break;
                            // si encontró con los dos puntos
                            default:
                                $explode = explode(':', str_replace(array(
                                    '{',
                                    '}'
                                ), '', $value));
                                switch ($explode[0]) {
                                    case 'bool':
                                        if ($arrayPath[$key] === 'false') {
                                            $this->params[$explode[1]] = false;
                                            $cntTmp ++;
                                        } else if ($arrayPath[$key] === 'true') {
                                            $this->params[$explode[1]] = true;
                                            $cntTmp ++;
                                        }
                                        break;
                                    case 'float':
                                        if (preg_match('/^[-]?([0-9]+).([0-9]+)$/', $arrayPath[$key])) {
                                            $this->params[$explode[1]] = (float) $arrayPath[$key];
                                            $cntTmp ++;
                                        }
                                        break;
                                    case 'int':
                                        if (preg_match('/^[-]?([0-9]+)$/', $arrayPath[$key])) {
                                            $this->params[$explode[1]] = (int) $arrayPath[$key];
                                            $cntTmp ++;
                                        }
                                        break;
                                    case 'integer':
                                        if (preg_match('/^[-]?([0-9]+)$/', $arrayPath[$key])) {
                                            $this->params[$explode[1]] = (integer) $arrayPath[$key];
                                            $cntTmp ++;
                                        }
                                        break;
                                    case 'string':
                                        if (preg_match('/^([a-zA-z])+$/', $arrayPath[$key])) {
                                            $this->params[$explode[1]] = (string) $arrayPath[$key];
                                            $cntTmp ++;
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
            } else if ($detail['path'] === $this->path) {
                $this->route = $detail;
                return true;
            }
        }
        return false;
    }

    /**
     * Devuelve el objeto del controlador asignado en la ruta
     * 
     * @return Controller
     */
    public function getController(): Controller
    {
        return new $this->route['controller']();
    }

    /**
     * Devuelve vista asignada en la ruta
     * 
     * @return string
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
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Devuelve falso o verdadero si encontró o no una ruta válida
     * 
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->is_valid;
    }

    /**
     * Devuelve un arreglo con las rutas del sistema
     * 
     * @throws ZOEException
     * @return array
     */
    protected function getRoutingFile(): array
    {
        try {
            if ($this->scope === self::DEV) {
                return Yaml::parseFile($this->path_proyect . self::YAML);
            } else {
                if (apcu_exists(self::NAME_CACHE) === true) {
                    return apcu_fetch(self::NAME_CACHE);
                } else if ($this->cache->has(Routing::CACHE) === true) {
                    apcu_add(self::NAME_CACHE, (array) json_decode($this->cache->get(Routing::CACHE), true));
                    return apcu_fetch(self::NAME_CACHE);
                } else {
                    apcu_add(self::NAME_CACHE, Yaml::parseFile($this->path_proyect . self::YAML));
                    $this->cache->set(Routing::CACHE, json_encode(apcu_fetch(self::NAME_CACHE), true));
                    return apcu_fetch(self::NAME_CACHE);
                }
            }
        } catch (ParseException $exc) {
            throw new ZOEException($exc->getMessage());
        }
    }
}
