<?php

/**
 * Archivo para testear el FrontController de Zoe EE
 *
 * PHP version 7
 *
 * @category Test
 * @package  Test
 * @author   Julian Lasso <jalasso69@misena.edu.co>
 * @license  http://www.google.com/ Apache2 License
 * @link     http://www.google.com/
 */

require '../vendor/autoload.php';

use ZoeEE\Controller\FrontController;

$path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'output' . DIRECTORY_SEPARATOR . 'ideal';
$app = new FrontController($path, FrontController::DEV);
$app->run();
