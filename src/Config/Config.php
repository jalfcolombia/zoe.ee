<?php

namespace ZoeEE\Config;

use ZoeEE\ExceptionHandler\ZOEException;
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
   * @param string $param Nombre del parámetro de configuración
   * @return mixed Valor contenido en el parámetro de configuración
   */
  public function Get(string $param)
  {
    $key = "['" . str_replace('.', "']['", $param) . "']";
    return eval('$this->GetConfig()' . $key);
  }

  public function GetConfig(): array
  {
    if ($this->cache->Has(Config::CACHE) === true) {
      return json_decode($this->cache->Get(Config::CACHE));
    }
    else {
      $config = yaml_parse_file(__DIR__ . self::YAML);
      $this->cache->Set(Config::CACHE, json_encode($config));
      return $config;
    }
  }

}
