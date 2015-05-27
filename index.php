<?php

/**
 * Copyright (c) 2014 Filip Sedláček (filsedla@gmail.com)
 */

use Filsedla\DbTool\DbTool;
use Tracy\Debugger;

require __DIR__ . '/bootstrap.php'; // $container

Debugger::$maxDepth = 10;
Debugger::$maxLen = 1000;

$rootDir = __DIR__;
$dbTool = new DbTool($rootDir, $container);
$dbTool->process();

