<?php
/**
 * This script is expected to be located somewhere inside your project, e.g. <project_root>/tools/db/dbtool.php
 * and run manually when needed (every time after you modify the project database's structure)
 *
 * Example integration
 */

// Load composer libraries
require __DIR__ . '/vendor/autoload.php';

// \Tracy\Debugger::enable();

// Load dbtool classes - REMOVE THESE LINES when loaded through composer
require __DIR__ . '/../src/' . 'Data.php';
require __DIR__ . '/../src/' . 'DbTool.php';
require __DIR__ . '/../src/' . 'Dump.php';

$dsn = 'mysql:host=localhost;dbname=nette_book_example;charset=utf8';
$user = 'root';
$password = '***'; // FILL
$dumpDir = __DIR__;

$dbTool = \Filsedla\DbTool\DbTool::createFromScratch(
    $dsn, $user, $password,
    __DIR__ // Dump directory
);

// Run
$dbTool->process();
