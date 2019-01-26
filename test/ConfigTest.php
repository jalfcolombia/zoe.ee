<?php

/*apcu_clear_cache();*/
/*echo '<pre>';
print_r(apcu_cache_info());
exit();*/

require '../vendor/autoload.php';

use ZoeEE\Cache\Cache;
use ZoeEE\Config\Config;

$config = new Config(
    new Cache(
        __DIR__ . '\\..\\output\\ideal\\',
        '.cache\\'
    ),
    'prod',
    __DIR__ . '\\..\\output\\ideal\\',
    'Inscripcion',
    'Voceros'
);
var_dump($config->get('personal'));
