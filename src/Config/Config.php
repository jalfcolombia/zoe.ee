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
 * Clase para manejar la configuración del sistema y los paquetes del proyecto
 *
 * @author Julian Lasso <jalasso69@misena.edu.co>
 * @package ZoeEE
 * @subpackage Config
 * @example El siguiente es un ejemplo básico de la estructura YAML del archivo Config.yml
 *          project: testZoeEE
 *          url: localhost|TestZoezoe.ee|Public|
 *          lang: en
 *          session:
 *          --name: TestSessionZoeEE
 *          --time: 3600
 *          public:
 *          --path: Public
 *          --css: css
 *          --javascript: js
 *          --images: img
 *          --upload: upload
 *          --download: download
 *          db:
 *          --driver: pgsql
 *          --host: localhost
 *          --port: 5432
 *          --user: postgres
 *          --password: 12345
 *          --database: db_proyecto
 */
class Config
{

    /**
     * Nombre del directorio donde se alojan los paquetes
     */
    private const DIR_BUNDLE = 'Bundle' . DIRECTORY_SEPARATOR;

    /**
     * Nombre del directorio donde se aloja la configuración general y de los paquetes
     */
    private const DIR_CONFIG = 'Config' . DIRECTORY_SEPARATOR;

    /**
     * Nombre de las variables en la caché de la memoría RAM
     */
    private const NAME_CACHE = 'zoeConfig';

    /**
     * Dirección y nombre de archivo en la caché
     */
    private const CACHE = self::DIR_CONFIG . 'Config';

    /**
     * Dirección y nombre del archivo YAML
     */
    private const YAML = self::DIR_CONFIG . 'Config.yml';

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
    private $path_proyect;

    /**
     * Constructor de la clase Config
     *
     * @param Cache $cache
     *            Objeto para manejar la caché del sistema.
     * @param string $scope
     *            Ambito en el que se ejecuta el proyecto. Ej: dev, proc o test
     * @param string $path_proyect
     *            Ruta física del proyecto en el servidor
     * @param string|null $bundle
     *            [opcional] Nombre del paquete a procesar
     */
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
     *            Nombre del par�metro de configuración
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
     * @return array Arreglo con la figuración del sistema más la configuración del paquete a usar
     */
    protected function getConfig(): array
    {
        try {
            if ($this->scope === self::DEV) {
                return $this->loadBundleConfigYaml($this->path_proyect . self::DIR_BUNDLE . $this->bundle . self::YAML, Yaml::parseFile($this->path_proyect . self::YAML));
            } else {
                if (apcu_exists(self::NAME_CACHE . DIRECTORY_SEPARATOR . $this->bundle) === true) {
                    return apcu_fetch(self::NAME_CACHE . DIRECTORY_SEPARATOR . $this->bundle);
                } else if ($this->cache->has(self::NAME_CACHE . DIRECTORY_SEPARATOR . $this->bundle) === true) {
                    apcu_add(self::NAME_CACHE . DIRECTORY_SEPARATOR . $this->bundle, (array) json_decode($this->cache->get(self::DIR_BUNDLE . $this->bundle . self::CACHE), true));
                    return apcu_fetch(self::NAME_CACHE . DIRECTORY_SEPARATOR . $this->bundle);
                } else {
                    apcu_add(self::NAME_CACHE . DIRECTORY_SEPARATOR . $this->bundle, $this->loadBundleConfigYaml($this->path_proyect . self::DIR_BUNDLE . $this->bundle . self::YAML, Yaml::parseFile($this->path_proyect . self::YAML)));
                    $this->cache->set(self::DIR_BUNDLE . $this->bundle . self::CACHE, json_encode(apcu_fetch(self::NAME_CACHE . DIRECTORY_SEPARATOR . $this->bundle), true));
                    return apcu_fetch(self::NAME_CACHE . DIRECTORY_SEPARATOR . $this->bundle);
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
