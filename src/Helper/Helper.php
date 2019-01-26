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
 * @category Helper
 * @package  ZoeEE
 * @author   Julian Lasso <jalasso69@misena.edu.co>
 * @license  https://github.com/jalfcolombia/zoe.ee/blob/master/LICENSE Apache2
 * @link     https://github.com/jalfcolombia/zoe.ee
 */

namespace ZoeEE\Helper;

use ZoeEE\Cache\Cache;
use Symfony\Component\Yaml\Yaml;
use ZoeEE\Controller\FrontController;
use ZoeEE\ExceptionHandler\ZOEException;

/**
 * Clase ayudante de funciones varias
 *
 * @category Helper
 * @package  ZoeEE
 * @author   Julian Lasso <jalasso69@misena.edu.co>
 * @license  https://github.com/jalfcolombia/zoe.ee/blob/master/LICENSE Apache2
 * @link     https://github.com/jalfcolombia/zoe.ee
 */
class Helper
{

    /**
     * Indica el nivel global de un archivo de configuración
     */
    public const GLODAL  = 0;

    /**
     * Indica en nivel del la configuraicón del Bundle
     */
    public const BUNDLE = 1;

    /**
     * Indica en nivel del la configuraicón del proyecto en el bundle
     */
    public const PROJECT = 2;

    /**
     * Indica en nivel del la configuraicón del Bundle en el proyecto
     */
    public const PROJECT_BUNDLE = 3;

    /**
     * Indica en nivel del archivo YAML
     */
    public const FILE_YAML = 0;

    /**
     * Indica en nivel del la llave para guardar en cache APC
     */
    public const APCU_KEY = 1;

    /**
     * Indica en nivel del archivo en caché
     */
    public const FILE_CACHE = 2;

    /**
     * Objeto para manejar la caché del sistema
     *
     * @var Cache
     */
    private $cache;

    /**
     * Constructor de la clase Helper
     *
     * @param Cache $cache Instancia del objeto caché del sistema
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Devuelve un arreglo con los datos unidos de diferentes archivos de configuracion
     *
     * @param array $data Arreglo con el siguiente estilo:
     *                    $files = array(
     *                      'global' => array(
     *                        'file_yaml' => '',
     *                        'apcu_key' => '',
     *                        'file_cache'
     *                      ),
     *                      'bundle' => array(
     *                        'file_yaml' => '',
     *                        'apcu_key' => '',
     *                        'file_cache'
     *                      ),
     *                      'project' => array(
     *                        'file_yaml' => '',
     *                        'apcu_key' => '',
     *                        'file_cache'
     *                      ),
     *                      'project_bundle' => array(
     *                        'file_yaml' => '',
     *                        'apcu_key' => '',
     *                        'file_cache'
     *                      ),
     *                    );
     * @param string      $scope   Ambito en el que está trabajando la aplicación (DEV, PROD, TEST)
     * @param string|null $project [opcional] Nombre del proyecto que está trabajando ubicado en la carpeta Bundle
     *
     * @return array Arreglo con los datos de configuración en los archivos encontrados
     */
    public function getSerialFiles(array $data, string $scope, ?string $project = null): array
    {
        /* [INICIO] Averigua la llave en APCu */
        if (is_file($data[self::GLODAL][self::FILE_YAML]) === false) {
            throw new ZOEException(
                ZOEException::F0003_MESSAGE,
                ZOEException::F0003_CODE
            );
        }
        $key = $data[self::GLODAL][self::APCU_KEY];
        $file_cache = $data[self::GLODAL][self::FILE_CACHE];
        $flag = 1;
        if ($project !== null) {
            if (is_file($data[self::PROJECT][self::FILE_YAML]) === true) {
                $key = $data[self::PROJECT][self::APCU_KEY];
                $file_cache = $data[self::PROJECT][self::FILE_CACHE];
                $flag += 2; // 3
            }
            if (is_file($data[self::PROJECT_BUNDLE][self::FILE_YAML]) === true) {
                $key = $data[self::PROJECT_BUNDLE][self::APCU_KEY];
                $file_cache = $data[self::PROJECT_BUNDLE][self::FILE_CACHE];
                $flag += 3; // 4 | 6
            }
        } else {
            if (is_file($data[self::BUNDLE][self::FILE_YAML]) === true) {
                $key = $data[self::BUNDLE][self::APCU_KEY];
                $file_cache = $data[self::BUNDLE][self::FILE_CACHE];
                $flag += 1; // 2
            }
        }
        /* [FIN] Averigua la llave en APCu */

        if ($scope === FrontController::DEV) {
            switch ($flag) {
                case 1:
                    return Yaml::parseFile($data[self::GLODAL][self::FILE_YAML]);
                    break;
                case 2:
                    return array_merge(
                        Yaml::parseFile($data[self::GLODAL][self::FILE_YAML]),
                        Yaml::parseFile($data[self::BUNDLE][self::FILE_YAML])
                    );
                    break;
                case 3:
                    return array_merge(
                        Yaml::parseFile($data[self::GLODAL][self::FILE_YAML]),
                        Yaml::parseFile($data[self::PROJECT][self::FILE_YAML])
                    );
                    break;
                case 4:
                    return array_merge(
                        Yaml::parseFile($data[self::GLODAL][self::FILE_YAML]),
                        Yaml::parseFile($data[self::PROJECT_BUNDLE][self::FILE_YAML])
                    );
                    break;
                case 6:
                    return array_merge(
                        Yaml::parseFile($data[self::GLODAL][self::FILE_YAML]),
                        Yaml::parseFile($data[self::PROJECT][self::FILE_YAML]),
                        Yaml::parseFile($data[self::PROJECT_BUNDLE][self::FILE_YAML])
                    );
                    break;
            }
        } elseif ($scope === FrontController::PROD) {
            if ($this->cache->has($file_cache) === true
                and apcu_exists($key) === false
            ) {
                apcu_add(
                    $key,
                    (array) json_decode($this->cache->get($file_cache), true)
                );
            } else {
                $cache = array();
                switch ($flag) {
                    case 1:
                        $cache = Yaml::parseFile(
                            $data[self::GLODAL][self::FILE_YAML]
                        );
                        break;
                    case 2:
                        $cache = array_merge(
                            Yaml::parseFile($data[self::GLODAL][self::FILE_YAML]),
                            Yaml::parseFile($data[self::BUNDLE][self::FILE_YAML])
                        );
                        break;
                    case 3:
                        $cache = array_merge(
                            Yaml::parseFile($data[self::GLODAL][self::FILE_YAML]),
                            Yaml::parseFile($data[self::PROJECT][self::FILE_YAML])
                        );
                        break;
                    case 4:
                        $cache = array_merge(
                            Yaml::parseFile($data[self::GLODAL][self::FILE_YAML]),
                            Yaml::parseFile(
                                $data[self::PROJECT_BUNDLE][self::FILE_YAML]
                            )
                        );
                        break;
                    case 6:
                        $cache = array_merge(
                            Yaml::parseFile($data[self::GLODAL][self::FILE_YAML]),
                            Yaml::parseFile($data[self::PROJECT][self::FILE_YAML]),
                            Yaml::parseFile($data[self::PROJECT_BUNDLE][self::FILE_YAML])
                        );
                        break;
                }
                apcu_add($key, $cache, true);
                $this->cache->set($file_cache, json_encode($cache));
            }
            return apcu_fetch($key);
        }
    }
}
