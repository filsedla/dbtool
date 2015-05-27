<?php
/**
 * Copyright (c) 2015 Filip Sedlacek <filsedla@gmail.com>
 */

namespace Filsedla\DbTool;

use Nette\Database\Context;
use Tracy\Debugger;

/** @var \SystemContainer $container */
$container = require __DIR__ . '/bootstrap.php';

/** @var Context $database */
$dbTool = $container->getByType('Filsedla\DbTool\DbTool');

Debugger::$maxDepth = 10;
Debugger::$maxLen = 1000;

$dbTool->process();