<?php

require '../vendor/autoload.php';

use ZoeEE\Routing\Routing;
use ZoeEE\Cache\Cache;

$routing = new Routing('/', new Cache(__DIR__ . '\\..\\output\\ideal\\', '.cache\\'), __DIR__ . '\\..\\output\\ideal\\', $_SERVER['REQUEST_METHOD'], (isset($_SERVER['HTTP_X_REQUESTED_WITH']) === true && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ? true : false, 'dev');
if ($routing->isValid() === true) {
    var_dump($routing->getParams());
    var_dump($routing->getRoute());
} else {
    echo 'dirección no válida';
}