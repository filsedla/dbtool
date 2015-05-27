<?php
/**
 * Copyright (c) 2015 Filip Sedlacek <filsedla@gmail.com>
 */

date_default_timezone_set('Europe/Prague');
header('X-Frame-Options: SAMEORIGIN');
header('Content-Type: text/html; charset=utf-8');

require __DIR__ . '/vendor/autoload.php';

$configurator = new \Nette\Configurator;
$configurator->setDebugMode(TRUE);
$configurator->enableDebugger(__DIR__ . '/log');
$configurator->setTempDirectory(__DIR__ . '/temp');

$configurator->createRobotLoader()
    ->addDirectory(__DIR__)
    ->register();

$configurator->addConfig(__DIR__ . '/config.neon');
$configurator->addConfig(__DIR__ . '/config.local.neon');

return $configurator->createContainer();
