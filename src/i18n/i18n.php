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

    private const DIR = 'i18n' . DIRECTORY_SEPARATOR;

    private const NAME_CACHE = 'zoei18n';

    /**
     * Dirección y nombre de archivo en la caché
     */
    private const CACHE = 'i18n' . DIRECTORY_SEPARATOR;

    /**
     * Dirección y nombre del archivo YAML
     */
    private const YAML = 'i18n' . DIRECTORY_SEPARATOR;

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

    private $scope;

    private $bundle;

    private $path_proyect;

    private $language;

    public function __construct(string $language, string $scope, Cache $cache, string $path_proyect, ?string $bundle = null)
    {
        $this->language = $language;
        $this->cache = $cache;
        $this->scope = $scope;
        $this->bundle = ($bundle !== null) ? $bundle . DIRECTORY_SEPARATOR : null;
        $this->path_proyect = $path_proyect;
    }

    /**
     * 
     * @param string $text
     * @param mixed $args [opcional]
     * @return string
     */
    public function __(string $text, $args): string
    {
        $dictionary = $this->getDictionary();
        if (is_string($args) === true) {
            return sprintf($dictionary[$text], $args);
        } elseif (is_array($args) === true) {
            return vsprintf($dictionary[$text], $args);
        }
    }

    /**
     * Devuelve un arreglo con la configuración del sistema
     *
     * @throws ZOEException
     * @return array
     */
    protected function getDictionary(): array
    {
        try {
            if ($this->scope === self::DEV) {
                return $this->loadBundleDictionaryYaml($this->path_proyect . self::DIR . $this->bundle . self::YAML . $this->language . '.yml', Yaml::parseFile($this->path_proyect . self::YAML . $this->language . '.yml'));
            } else {
                if (apcu_exists(self::NAME_CACHE . $this->bundle . $this->language) === true) {
                    return apcu_fetch(self::NAME_CACHE . $this->bundle . $this->language);
                } else if ($this->cache->has(self::CACHE . $this->bundle . $this->language) === true) {
                    apcu_add(self::NAME_CACHE . $this->bundle . $this->language, (array) json_decode($this->cache->get(self::CACHE . $this->bundle . $this->language), true));
                    return apcu_fetch(self::NAME_CACHE . $this->bundle . $this->language);
                } else {
                    apcu_add(self::NAME_CACHE . $this->bundle . $this->language, $this->loadBundleDictionaryYaml($this->path_proyect . self::DIR . $this->bundle . self::YAML . $this->language . '.yml', Yaml::parseFile($this->path_proyect . self::YAML . $this->language . '.yml')));
                    $this->cache->set(self::CACHE . $this->bundle . $this->language, json_encode(apcu_fetch(self::NAME_CACHE . $this->bundle . $this->language), true));
                    return apcu_fetch(self::NAME_CACHE . $this->bundle . $this->language);
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
     * @param array $i18nInit
     * @return array
     */
    protected function loadBundleDictionaryYaml(string $file, array $i18nInit): array
    {
        if (is_file($file) === true) {
            $i18nInit = array_merge($i18nInit, Yaml::parseFile($file));
        }
        return $i18nInit;
    }
}
