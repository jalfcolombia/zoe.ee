<?php

require '../vendor/autoload.php';

use ZoeEE\Routing\Routing;
use ZoeEE\Cache\Cache;

$routing = new Routing('/julian/33.html', new Cache(__DIR__ . '\\..\\output\\ideal\\', '.cache\\'), __DIR__ . '\\..\\output\\ideal\\', 'dev');
if ($routing->isValid() === true) {
    echo $routing->GetView();
    echo '<br><pre>';
    var_dump($routing->getParams());
    var_dump($routing->getRoute());
} else {
    echo 'dirección no válida';
}