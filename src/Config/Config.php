<?php

namespace ZoeEE\Config;

use ZoeEE\Cache\Cache;

/**
 * Class Config
 *
 * @package ZoeEE\Config
 */
class Config
{

    /**
     * Dirección y nombre de archivo en la caché
     */
    private const CACHE = 'Confing/Config';

    /**
     * Dirección y nombre del archivo YAML
     */
    private const YAML = 'Config/Config.yml';

    /**
     * Objeto para manejar el caché del sistema
     *
     * @var Cache
     */
    private $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Devuelve el valor del parámetro de configuración.<br>
     * Ejemplo: path, public.path, db.driver
     *
     * @param string $param
     *            Nombre del parámetro de configuración
     * @return mixed Valor contenido en el parámetro de configuración
     */
    public function Get(string $param)
    {
        $key = "['" . str_replace('.', "']['", $param) . "']";
        return eval('$this->GetConfig()' . $key);
    }

    public function GetConfig(): array
    {
        if ($this->cache->has(self::CACHE) === true) {
            return json_decode($this->cache->get(self::CACHE));
        } else {
            $config = yaml_parse_file(self::YAML);
            $this->cache->Set(self::CACHE, json_encode($config));
            return $config;
        }
    }
}
