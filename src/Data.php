<?php

/**
 * Copyright (c) 2014 Filip Sedláček (filsedla@gmail.com)
 */

namespace Filsedla\DbTool;

use Nette\Utils\DateTime;

final class Data
{
    public static function header()
    {
        return
            "-- DB tool MySQL dump\n" .
            "\n" .
            "SET NAMES utf8;\n" .
            "SET time_zone = '+00:00';\n" .
            "SET foreign_key_checks = 0;\n" .
            "SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';\n" .
            "\n";
    }

    public static function footer()
    {
        $now = new DateTime();
        return
            "\n" .
            "\n" .
            "-- {$now->format('Y-m-d H:i:s')}\n";
    }
}