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

namespace ZoeEE\i18n;

use ZoeEE\Cache\Cache;
use ZoeEE\Helper\Helper;
use ZoeEE\ExceptionHandler\ZOEException;

/**
 * Clase para manejar la internacionalización de los mensajes en el sistema
 *
 * @author Julian Lasso <jalasso69@misena.edu.co>
 * @package ZoeEE
 * @subpackage i18n
 */
class i18n
{

    /**
     * Nombre del directorio de los paquetes por defecto
     */
    private const DIR_BUNDLE = 'Bundle' . DIRECTORY_SEPARATOR;

    /**
     * Nombre del directorio donde se encuentran los diccionarios
     */
    private const DIR_I18N = 'i18n' . DIRECTORY_SEPARATOR;

    /**
     * Nombre del caché para los diccionarios en memoria RAM
     */
    private const NAME_CACHE = 'zoei18n' . DIRECTORY_SEPARATOR;

    /**
     * Dirección y nombre del archivo YAML
     */
    private const YAML = '.yml';

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
     * Ambito en el que corre el sistema
     *
     * @var string
     */
    private $scope;

    /**
     * Nombre del paquete a usar en el controlador frontal
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
     * Lenguaje a usar para el sistema
     *
     * @var string
     */
    private $language;

    private $project;

    private $helper;

    /**
     * Constructor de la clase i18n
     *
     * @param string $language Lenguaje a usar para el sistema
     * @param string $scope Ambito en el que corre el sistema
     * @param Cache $cache Objeto para el manejo de caché del sistema
     * @param string $path_project Ruta física del proyecto en el servidor
     * @param string $bundle Nombre del paquete a usar en el controlador frontal
     * @param string|null $project [opcional] Nombre del projecto a usar en el controlador frontal
     */
    public function __construct(string $language, string $scope, Cache $cache, string $path_project, string $bundle, ?string $project = null)
    {
        $this->language = $language;
        $this->cache = $cache;
        $this->scope = $scope;
        $this->bundle = $bundle . DIRECTORY_SEPARATOR;
        $this->path_project = $path_project;
        $this->project = $project; // ($project !== null) ? $project . DIRECTORY_SEPARATOR : null;
        $this->helper = new Helper($cache);
    }

    /**
     * Devuelve el mensaje solicitado
     *
     * @param string $text Indice del mensaje a usar
     * @param mixed $args [opcional] Arreglo o cadena de carácteres a usar en el mensaje
     * @return string Cadena de caracteres con el mensaje solicitado
     */
    public function __(string $text, $args = null): string
    {
        $dictionary = $this->getDictionary();
        if (is_string($args) === true) {
            return sprintf($dictionary[$text], $args);
        } elseif (is_array($args) === true) {
            return vsprintf($dictionary[$text], $args);
        }
        return $dictionary[$text];
    }

    /**
     * Establece el lenguaje a usar en el sistema
     *
     * @param string $language
     * @return i18n
     */
    public function setLanguage(string $language): i18n
    {
        $this->language = $language;
        return $this;
    }

    /**
     * Devuelve un arreglo con la configuración del sistema
     *
     * @throws ZOEException
     * @return array
     */
    protected function getDictionary(): array
    {
        $data = array(
            Helper::GLOBAL => array(
                Helper::FILE_YAML => $this->path_project . self::DIR_I18N . $this->language . self::YAML,
                Helper::APCU_KEY => self::NAME_CACHE . $this->path_project . self::DIR_I18N . $this->language,
                Helper::FILE_CACHE => self::DIR_I18N . $this->language
            ),
            Helper::BUNDLE => array(
                Helper::FILE_YAML => $this->path_project . self::DIR_BUNDLE. $this->bundle . self::DIR_I18N . $this->language . self::YAML,
                Helper::APCU_KEY => self::NAME_CACHE . $this->path_project . self::DIR_BUNDLE . $this->bundle . self::DIR_I18N . $this->language,
                Helper::FILE_CACHE => self::DIR_BUNDLE . $this->bundle . self::DIR_I18N . $this->language
            ),
            Helper::PROJECT => array(
                Helper::FILE_YAML => $this->path_project . self::DIR_BUNDLE . $this->project . DIRECTORY_SEPARATOR . self::DIR_I18N . $this->language . self::YAML,
                Helper::APCU_KEY => self::NAME_CACHE . $this->path_project . self::DIR_BUNDLE . $this->project . DIRECTORY_SEPARATOR . self::DIR_I18N . $this->language,
                Helper::FILE_CACHE => self::DIR_BUNDLE . $this->project . DIRECTORY_SEPARATOR . self::DIR_I18N . $this->language
            ),
            Helper::PROJECT_BUNDLE => array(
                Helper::FILE_YAML => $this->path_project . self::DIR_BUNDLE . $this->project . DIRECTORY_SEPARATOR . self::DIR_BUNDLE . $this->bundle . self::DIR_I18N . $this->language . self::YAML,
                Helper::APCU_KEY => self::NAME_CACHE . $this->path_project . self::DIR_BUNDLE . $this->project . DIRECTORY_SEPARATOR . self::DIR_BUNDLE . $this->bundle . self::DIR_I18N . $this->language,
                Helper::FILE_CACHE => self::DIR_BUNDLE . $this->project . DIRECTORY_SEPARATOR . self::DIR_BUNDLE . $this->bundle . self::DIR_I18N . $this->language
            )
        );
        return $this->helper->getSerialFiles($data, $this->scope, $this->project);
    }
}
