<?php

/**
 * @author Filip Sedlacek <sedlacek@webtoad.cz>
 */

// Load libraries (VENDOR)
require __DIR__ . '/../libs/autoload.php';

// Create Nette Configurator
$configurator = new Nette\Configurator();
$configurator->setDebugMode(TRUE);
$configurator->enableDebugger(__DIR__ . '/../log');
$configurator->setTempDirectory(__DIR__ . '/../temp');

// Create Nette robot loader
$configurator->createRobotLoader()
    ->addDirectory(__DIR__ . '/../app/') // Main application directory (APP)
    ->addDirectory(__DIR__ . '/../libs') // Main application additional libraries (LIBS)
    ->addDirectory(__DIR__) // DB_TOOL directory
    ->register();

// Load main application configs
$configurator->addConfig(__DIR__ . '/../app/config/config.neon', $configurator::AUTO);
$configurator->addConfig(__DIR__ . '/../app/config/config.local.neon');

// Create DI container
$container = $configurator->createContainer();

//$_SERVER = array_intersect_key($_SERVER, array_flip(array('PHP_SELF', 'SCRIPT_NAME', 'SERVER_ADDR', 'SERVER_SOFTWARE', 'HTTP_HOST', 'DOCUMENT_ROOT', 'OS', 'argc', 'argv')));
//$_SERVER['REQUEST_TIME'] = 1234567890;
//$_ENV = $_GET = $_POST = array();
