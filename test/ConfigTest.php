<?php

require '../vendor/autoload.php';

use ZoeEE\Cache\Cache;
use ZoeEE\Config\Config;

$config = new Config(new Cache(__DIR__ . '\\..\\output\\ideal\\', '.cache\\'), 'prod', __DIR__ . '\\..\\output\\ideal\\', 'Logon');
var_dump($config->get('personal'));