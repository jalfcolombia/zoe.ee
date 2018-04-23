<?php

/**
 * This file is part of the ZoeEE package.
 *
 * (c) Julian Lasso <jalasso69@misena.edu.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZoeEE\Config;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;
use ZoeEE\Cache\Cache;
use ZoeEE\ExceptionHandler\ZOEException;

/**
 * Class Config
 *
 * @author Julian Lasso <jalasso69@misena.edu.co>
 * @package ZoeEE
 * @subpackage Config
 */
class Config
{

    private const DIR = 'Bundle' . DIRECTORY_SEPARATOR;

    private const NAME_CACHE = 'zoeConfig';

    /**
     * Dirección y nombre de archivo en la caché
     */
    private const CACHE = 'Confing' . DIRECTORY_SEPARATOR . 'Config';

    /**
     * Dirección y nombre del archivo YAML
     */
    private const YAML = 'Config' . DIRECTORY_SEPARATOR . 'Config.yml';

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

    public function __construct(Cache $cache, string $scope, string $path_proyect, ?string $bundle = null)
    {
        $this->cache = $cache;
        $this->scope = $scope;
        $this->bundle = ($bundle !== null) ? $bundle . DIRECTORY_SEPARATOR : null;
        $this->path_proyect = $path_proyect;
    }

    /**
     * Devuelve el valor del parámetro de configuración.<br>
     * Ejemplo: url, public.path, db.driver
     *
     * @param string $param
     *            Nombre del parámetro de configuración
     * @return mixed Valor contenido en el parámetro de configuración
     */
    public function get(string $param)
    {
        $key = "['" . str_replace('.', "']['", $param) . "']";
        eval('$answer = (isset($this->getConfig()' . $key . ')) ? $this->getConfig()' . $key . ' : null;');
        return $answer;
    }

    /**
     * Devuelve un arreglo con la configuración del sistema
     *
     * @throws ZOEException
     * @return array
     */
    protected function getConfig(): array
    {
        try {
            if ($this->scope === self::DEV) {
                return $this->loadBundleConfigYaml($this->path_proyect . self::DIR . $this->bundle . self::YAML, Yaml::parseFile($this->path_proyect . self::YAML));
            } else {
                if (apcu_exists(self::NAME_CACHE . $this->bundle) === true) {
                    return apcu_fetch(self::NAME_CACHE . $this->bundle);
                } else if ($this->cache->has(self::CACHE . $this->bundle) === true) {
                    apcu_add(self::NAME_CACHE . $this->bundle, (array) json_decode($this->cache->get(self::CACHE . $this->bundle), true));
                    return apcu_fetch(self::NAME_CACHE . $this->bundle);
                } else {
                    apcu_add(self::NAME_CACHE . $this->bundle, $this->loadBundleConfigYaml($this->path_proyect . self::DIR . $this->bundle . self::YAML, Yaml::parseFile($this->path_proyect . self::YAML)));
                    $this->cache->set(self::CACHE . $this->bundle, json_encode(apcu_fetch(self::NAME_CACHE . $this->bundle), true));
                    return apcu_fetch(self::NAME_CACHE . $this->bundle);
                }
            }
        } catch (ParseException $exc) {
            throw new ZOEException($exc->getMessage());
        }
    }

    /**
     * Busca en los paquetes del sistema el archivo Config/Routing.yml para devolver un arreglo con las rutas del sistema
     *
     * @param string $file
     * @param array $configInit
     * @return array
     */
    protected function loadBundleConfigYaml(string $file, array $configInit): array
    {
        if (is_file($file) === true) {
            $configInit = array_merge($configInit, Yaml::parseFile($file));
        }
        return $configInit;
    }
}
