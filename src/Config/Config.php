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
 * @category Config
 * @package  ZoeEE
 * @author   Julian Lasso <jalasso69@misena.edu.co>
 * @license  https://github.com/jalfcolombia/zoe.ee/blob/master/LICENSE Apache2
 * @link     https://github.com/jalfcolombia/zoe.ee
 */

namespace ZoeEE\Config;

use ZoeEE\Cache\Cache;
use ZoeEE\Helper\Helper;
use ZoeEE\ExceptionHandler\ZOEException;

/**
 * Clase para manejar la configuración del sistema y los paquetes del proyecto
 *
 * @category Config
 * @package  ZoeEE
 * @author   Julian Lasso <jalasso69@misena.edu.co>
 * @license  https://github.com/jalfcolombia/zoe.ee/blob/master/LICENSE Apache2
 * @link     https://github.com/jalfcolombia/zoe.ee
 * @example  El siguiente es un ejemplo básico de la estructura YAML
 *           del archivo Config.yml
 *           url: localhost/TestZoezoe.ee/Public/
 *           lang: en
 *           session:
 *           ..name: TestSessionZoeEE
 *           ..time: 3600 # en minutos 3600 = 1 hora
 *           public:
 *           ..path: Public
 *           ..css: css
 *           ..javascript: js
 *           ..images: img
 *           ..upload: upload
 *           ..download: download
 *           db:
 *           ..driver: pgsql
 *           ..host: localhost
 *           ..port: 5432
 *           ..user: postgres
 *           ..password: 12345
 *           ..database: db_proyecto
 */
class Config
{

    /**
     * Nombre del directorio donde se alojan los paquetes
     */
    private const DIR_BUNDLE = 'Bundle' . DIRECTORY_SEPARATOR;

    /**
     * Nombre del directorio donde se aloja la configuración general y
     * de los paquetes
     */
    private const DIR_CONFIG = 'Config' . DIRECTORY_SEPARATOR;

    /**
     * Nombre de las variables en la caché de la memoría RAM
     */
    private const NAME_CACHE = 'zoeConfig' . DIRECTORY_SEPARATOR;

    /**
     * Dirección y nombre de archivo en la caché
     */
    private const CACHE = 'Config';

    /**
     * Dirección y nombre del archivo YAML
     */
    private const YAML = 'Config.yml';

    /**
     * Ambito en el que se ejecuta el proyecto.
     * Ej: dev, proc o test
     *
     * @var string
     */
    private $scope;

    /**
     * Nombre del paquete a procesar
     *
     * @var string
     */
    private $bundle;

    /**
     * Ruta física del proyecto en el servidor
     *
     * @var string
     */
    private $path_project;

    /**
     * Nombre del proyecto
     *
     * @var string
     */
    private $project;

    /**
     * Variable para manejar el objeto Helper
     *
     * @var Helper
     */
    private $helper;

    /**
     * Constructor de la clase Config
     *
     * @param string $scope        Ambito en el que se ejecuta el proyecto.
     *                             Ej: dev, proc o test
     * @param string $path_project Ruta física del proyecto en el servidor
     * @param string $bundle       Nombre del paquete a procesar
     * @param string $project      [opcional] Nombre del proyecto a procesar
     */
    public function __construct(
            Cache $cache,
            string $scope,
            string $path_project,
            string $bundle,
            ?string $project = null
    )
    {
        $this->scope        = $scope;
        $this->bundle       = $bundle . DIRECTORY_SEPARATOR;
        $this->path_project = $path_project;
        $this->project      = $project;
        $this->helper       = new Helper($cache);
    }

    /**
     * Devuelve el valor del parámetro de configuración.
     * Ejemplo: url, public.path, db.driver
     *
     * @param string $param Nombre del parámetro
     *
     * @return mixed Valor contenido en el parámetro
     */
    public function get(string $param)
    {
        $answer = null;
        $key    = "['" . str_replace('.', "']['", $param) . "']";
        $eval   = '$answer = (isset($this->getConfig()' . $key . ')) ';
        $eval   .= '? $this->getConfig()' . $key . ' : null;';
        eval($eval);
        return $answer;
    }

    public function getAll()
    {
        return $this->getConfig();
    }

    /**
     * Devuelve un arreglo con la configuración del sistema
     *
     * @throws ZOEException
     *
     * @return array Arreglo con la figuración del sistema más la configuración
     *               del paquete a usar
     */
    protected function getConfig(): array
    {
        $data = array(
            Helper::GLODAL         => array(
                Helper::FILE_YAML  => $this->path_project
                . self::DIR_CONFIG
                . self::YAML,
                Helper::APCU_KEY   => self::NAME_CACHE
                . $this->path_project
                . self::DIR_CONFIG . self::CACHE,
                Helper::FILE_CACHE => self::DIR_CONFIG . self::CACHE
            ),
            Helper::BUNDLE         => array(
                Helper::FILE_YAML  => $this->path_project
                . self::DIR_BUNDLE
                . $this->bundle
                . self::DIR_CONFIG
                . self::YAML,
                Helper::APCU_KEY   => self::NAME_CACHE
                . $this->path_project
                . self::DIR_BUNDLE
                . $this->bundle
                . self::DIR_CONFIG . self::CACHE,
                Helper::FILE_CACHE => self::DIR_BUNDLE
                . $this->bundle
                . self::DIR_CONFIG
                . self::CACHE
            ),
            Helper::PROJECT        => array(
                Helper::FILE_YAML  => $this->path_project
                . self::DIR_BUNDLE
                . $this->project . DIRECTORY_SEPARATOR
                . self::DIR_CONFIG
                . self::YAML,
                Helper::APCU_KEY   => self::NAME_CACHE
                . $this->path_project
                . self::DIR_BUNDLE
                . $this->project . DIRECTORY_SEPARATOR
                . self::DIR_CONFIG
                . self::CACHE,
                Helper::FILE_CACHE => self::DIR_BUNDLE
                . $this->project . DIRECTORY_SEPARATOR
                . self::DIR_CONFIG
                . self::CACHE
            ),
            Helper::PROJECT_BUNDLE => array(
                Helper::FILE_YAML  => $this->path_project
                . self::DIR_BUNDLE
                . $this->project . DIRECTORY_SEPARATOR
                . self::DIR_BUNDLE
                . $this->bundle
                . self::DIR_CONFIG
                . self::YAML,
                Helper::APCU_KEY   => self::NAME_CACHE
                . $this->path_project
                . self::DIR_BUNDLE
                . $this->project . DIRECTORY_SEPARATOR
                . self::DIR_BUNDLE
                . $this->bundle
                . self::DIR_CONFIG
                . self::CACHE,
                Helper::FILE_CACHE => self::DIR_BUNDLE
                . $this->project . DIRECTORY_SEPARATOR
                . self::DIR_BUNDLE
                . $this->bundle
                . self::DIR_CONFIG
                . self::CACHE
            )
        );
        return $this->helper->getSerialFiles($data, $this->scope, $this->project);
    }

}
