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

namespace ZoeEE\Helper;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;
use ZoeEE\Cache\Cache;
use ZoeEE\ExceptionHandler\ZOEException;

class Helper {

    public const GLOBAL = 0;
    public const BUNDLE = 1;
    public const PROJECT = 2;
    public const PROJECT_BUNDLE = 3;
    public const FILE_YAML = 0;
    public const APCU_KEY = 1;
    public const FILE_CACHE = 2;

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

    private const NAME_CACHE = 'zoeConfig';

    private $cache;

    public function __construct(Cache $cache) {
        $this->cache = $cache;
    }

    public function getSerialFiles(array $data, string $scope, ?string $project = null): array {
        /* $files = array(
            'global' => array(
                'file' => '',
                'apcu_key' => '',
                'file_cache'
            ),
            'bundle' => array(
                'file' => '',
                'apcu_key' => '',
                'file_cache'
            ),
            'project' => array(
                'file' => '',
                'apcu_key' => '',
                'file_cache'
            ),
            'project_bundle' => array(
                'file' => '',
                'apcu_key' => '',
                'file_cache'
            ),
        ); */

        /* [INICIO] Averigua la llave en APCu */

        if (is_file($data[self::GLOBAL][self::FILE_YAML]) === false) {
            throw new ZOEException(ZOEException::F0003_MESSAGE, ZOEException::F0003_CODE);
        }
        $key = $data[self::GLOBAL][self::APCU_KEY];
        $file_cache = $data[self::GLOBAL][self::FILE_CACHE];
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

        if ($scope === self::DEV) {
            switch ($flag) {
                case 1:
                    return Yaml::parseFile($data[self::GLOBAL][self::FILE_YAML]);
                    break;
                case 2:
                    return array_merge(Yaml::parseFile($data[self::GLOBAL][self::FILE_YAML]), Yaml::parseFile($data[self::BUNDLE][self::FILE_YAML]));
                    break;
                case 3:
                    var_dump(array_merge(Yaml::parseFile($data[self::GLOBAL][self::FILE_YAML]), Yaml::parseFile($data[self::PROJECT][self::FILE_YAML])));
                    return array_merge(Yaml::parseFile($data[self::GLOBAL][self::FILE_YAML]), Yaml::parseFile($data[self::PROJECT][self::FILE_YAML]));
                    beak;
                case 4:
                    return array_merge(Yaml::parseFile($data[self::GLOBAL][self::FILE_YAML]), Yaml::parseFile($data[self::PROJECT_BUNDLE][self::FILE_YAML]));
                    break;
                case 6:
                    return array_merge(Yaml::parseFile($data[self::GLOBAL][self::FILE_YAML]), Yaml::parseFile($data[self::PROJECT][self::FILE_YAML]), Yaml::parseFile($data[self::PROJECT_BUNDLE][self::FILE_YAML]));
                    break;
            }
        } else if ($scope === self::PROD) {
            if ($this->cache->has($file_cache) === true and apcu_exists($key) === false) {
                apcu_add($key, (array) json_decode($this->cache->get($file_cache), true));
            } else {
                $cache = array();
                switch ($flag) {
                    case 1:
                        $cache = Yaml::parseFile($data[self::GLOBAL][self::FILE_YAML]);
                        break;
                    case 2:
                        $cache = array_merge(Yaml::parseFile($data[self::GLOBAL][self::FILE_YAML]), Yaml::parseFile($data[self::BUNDLE][self::FILE_YAML]));
                        break;
                    case 3:
                        $cache = array_merge(Yaml::parseFile($data[self::GLOBAL][self::FILE_YAML]), Yaml::parseFile($data[self::PROJECT][self::FILE_YAML]));
                        break;
                    case 4:
                        $cache = array_merge(Yaml::parseFile($data[self::GLOBAL][self::FILE_YAML]), Yaml::parseFile($data[self::PROJECT_BUNDLE][self::FILE_YAML]));
                        break;
                    case 6:
                        $cache = array_merge(Yaml::parseFile($data[self::GLOBAL][self::FILE_YAML]), Yaml::parseFile($data[self::PROJECT][self::FILE_YAML]), Yaml::parseFile($data[self::PROJECT_BUNDLE][self::FILE_YAML]));
                        break;
                }
                apcu_add($key, $cache, true);
                $this->cache->set($file_cache, json_encode($cache));
            }
            return apcu_fetch($key);
        }
    }

}