<?php

error_reporting(E_ALL | E_STRICT);

$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->add('Doctrine\Tests', __DIR__ . '/../vendor/doctrine/dbal/tests/');
