<?php

/**
 * This file is part of the ZoeEE package.
 *
 * (c) Julian Lasso <jalasso69@misena.edu.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZoeEE\i18n;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;
use ZoeEE\Cache\Cache;
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
    private const NAME_CACHE = 'zoei18n';

    /**
     * Dirección y nombre de archivo en la caché
     */
    private const CACHE = self::DIR_I18N;

    /**
     * Dirección y nombre del archivo YAML
     */
    private const YAML = self::DIR_I18N;

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
    private $path_proyect;

    /**
     * Lenguaje a usar para el sistema
     *
     * @var string
     */
    private $language;

    /**
     * Constructor de la clase i18n
     *
     * @param string $language
     *            Lenguaje a usar para el sistema
     * @param string $scope
     *            Ambito en el que corre el sistema
     * @param Cache $cache
     *            Objeto para el manejo de caché del sistema
     * @param string $path_proyect
     *            Ruta física del proyecto en el servidor
     * @param string|null $bundle
     *            Nombre del paquete a usar en el controlador frontal
     */
    public function __construct(string $language, string $scope, Cache $cache, string $path_proyect, ?string $bundle = null)
    {
        $this->language = $language;
        $this->cache = $cache;
        $this->scope = $scope;
        $this->bundle = ($bundle !== null) ? $bundle . DIRECTORY_SEPARATOR : null;
        $this->path_proyect = $path_proyect;
    }

    /**
     * Devuelve el mensaje solicitado
     *
     * @param string $text
     *            Indice del mensaje a usar
     * @param mixed $args
     *            [opcional] Arreglo o cadena de carácteres a usar en el mensaje
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
     * Devuelve un arreglo con la configuraci�n del sistema
     *
     * @throws ZOEException
     * @return array
     */
    protected function getDictionary(): array
    {
        try {
            if ($this->scope === self::DEV) {
                if (is_file($this->path_proyect . self::YAML . $this->language . '.yml') === true) {
                    return $this->loadBundleDictionaryYaml($this->path_proyect . self::DIR_BUNDLE . $this->bundle . self::YAML . $this->language . '.yml', Yaml::parseFile($this->path_proyect . self::YAML . $this->language . '.yml'));
                } else {
                    return Yaml::parseFile($this->path_proyect . self::DIR_BUNDLE . $this->bundle . self::YAML . $this->language . '.yml');
                }
            } else {
                if (apcu_exists(self::NAME_CACHE . DIRECTORY_SEPARATOR . $this->bundle . $this->language) === true) {
                    return apcu_fetch(self::NAME_CACHE . DIRECTORY_SEPARATOR . $this->bundle . $this->language);
                } else if ($this->cache->has(self::DIR_BUNDLE . $this->bundle . self::CACHE . $this->language) === true) {
                    apcu_add(self::NAME_CACHE . DIRECTORY_SEPARATOR . $this->bundle . $this->language, (array) json_decode($this->cache->get(self::DIR_BUNDLE . $this->bundle . self::CACHE . $this->language), true));
                    return apcu_fetch(self::NAME_CACHE . DIRECTORY_SEPARATOR . $this->bundle . $this->language);
                } else {
                    if (is_file($this->path_proyect . self::YAML . $this->language . '.yml') === true) {
                        apcu_add(self::NAME_CACHE . DIRECTORY_SEPARATOR . $this->bundle . $this->language, $this->loadBundleDictionaryYaml($this->path_proyect . self::DIR_BUNDLE . $this->bundle . self::YAML . $this->language . '.yml', Yaml::parseFile($this->path_proyect . self::YAML . $this->language . '.yml')));
                    } else {
                        apcu_add(self::NAME_CACHE . DIRECTORY_SEPARATOR . $this->bundle . $this->language, Yaml::parseFile($this->path_proyect . self::DIR_BUNDLE . $this->bundle . self::YAML . $this->language . '.yml'));
                    }
                    $this->cache->set(self::DIR_BUNDLE . $this->bundle . self::CACHE . $this->language, json_encode(apcu_fetch(self::NAME_CACHE . DIRECTORY_SEPARATOR . $this->bundle . $this->language), true));
                    return apcu_fetch(self::NAME_CACHE . DIRECTORY_SEPARATOR . $this->bundle . $this->language);
                }
            }
        } catch (ParseException $exc) {
            throw new ZOEException($exc->getMessage());
        }
    }

    /**
     * Busca en los paquetes del sistema el archivo i18n/en.yml para devolver un arreglo diccionario del idioma
     *
     * @param string $file
     *            Ruta y archivo donde se encuentra un diccionario
     * @param array $i18nInit
     *            Arreglo con diccionario previo
     * @return array Arreglo con la unión de los diccioanrios
     */
    protected function loadBundleDictionaryYaml(string $file, array $i18nInit): array
    {
        if (is_file($file) === true) {
            $i18nInit = array_merge($i18nInit, Yaml::parseFile($file));
        }
        return $i18nInit;
    }
}
