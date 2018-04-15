<?php
namespace ZoeEE\Routing;

use ZoeEE\Controller\Controller;
use ZoeEE\Cache\Cache;

class Routing
{

    /**
     * Dirección y nombre de archivo en la caché
     */
    private const CACHE = 'Confing/Routing';

    /**
     * Dirección y nombre del archivo YAML
     */
    private const YAML = 'Config/Routing.yml';

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

    public function GetController(): Controller
    {}

    public function GetView(): string
    {}

    protected function GetRouting(): array
    {
        if ($this->cache->Has(Routing::CACHE) === true) {
            return json_decode($this->cache->Get(Routing::CACHE));
        } else {
            $config = yaml_parse_file(self::YAML);
            $this->cache->Set(Routing::CACHE, json_encode($config));
            return $config;
        }
    }
}
