<?php
/**
 * This script is expected to be located somewhere inside your project, e.g. <project_root>/tools/db/dbtool.php
 * and run manually when needed (every time after you modify the project database's structure)
 *
 * Example integration into a Nette project
 *
 * Note: this example is not functional here
 */

/** @var \Nette\DI\Container $container */
$container = require __DIR__ . '/../../app/bootstrap.php'; // application bootstrap - ADJUST path

$dbTool = new \Filsedla\DbTool\DbTool(
    $container->getService('database.context'), // Context of the database to dump
    __DIR__ // Dump directory
);

// Run
$dbTool->process();
