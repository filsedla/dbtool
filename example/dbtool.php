<?php
// This file is expected to be located somewhere inside your project, e.g. <project_root>/tools/db/dbtool.php
// and run manually when needed (every time after you modify the project database's structure)

// Require the application bootstrap
/** @var \Nette\DI\Container $container */
$container = require __DIR__ . '/bootstrap.php'; // adjust

// Create DbTool service
/** @var \Filsedla\DbTool\DbTool $dbTool */
$dbTool = $container->createInstance(\Filsedla\DbTool\DbTool::class, [
   $container->getByType(\Nette\Database\Context::class), // Context of the database to dump
   __DIR__ // DB Dump directory
]);

// Run
$dbTool->process();
