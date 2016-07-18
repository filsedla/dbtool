<?php

/**
 * Copyright (c) 2014 Filip Sedláček (filsedla@gmail.com)
 */

namespace Filsedla\DbTool;

use Nette\Database\Context;
use Nette\Utils\Strings;

final class DbTool
{
    /** @var Context */
    private $db;

    /** @var string */
    private $databaseName;

    /** @var string */
    private $dumpDir;

    /**
     * @param Context $context
     * @param string $dumpDir
     */
    function __construct(Context $context, $dumpDir)
    {
        $this->db = $context;
        $this->dumpDir = $dumpDir;
        $this->fetchDatabaseName();
    }

    /**
     * @param string $dsn
     * @param string $user
     * @param string $password
     * @param string $dumpDir
     * @return self
     */
    public static function createFromScratch($dsn, $user, $password, $dumpDir)
    {
        $connection = new \Nette\Database\Connection($dsn, $user, $password);
        $structure = new \Nette\Database\Structure($connection, new \Nette\Caching\Storages\DevNullStorage());
        $context = new \Nette\Database\Context($connection, $structure);
        return new self($context, $dumpDir);
    }

    private function fetchDatabaseName()
    {
        $row = $this->db->query('SELECT DATABASE()')->fetch();
        $name = $row['DATABASE()'];
        $this->databaseName = $name;
    }

    public function process()
    {
        echo "-------------- <em>Processing tables</em> --------------<br>\n";
        $this->processTables();

        echo "-------------- <em>Processing views</em> --------------<br>\n";
        $this->processViews();

        echo "-------------- <em>Processing triggers</em> --------------<br>\n";
        $this->processTriggers();

        echo "-------------- <em>Processing procedures</em> --------------<br>\n";
        $this->processProcedures();

        echo "-------------- <em>Processing functions</em> --------------<br>\n";
        $this->processFunctions();

        echo '<br><em>OK</em><br>';
    }

    private function processProcedures()
    {
        $listResult = $this->db->query('SHOW PROCEDURE STATUS')->fetchAll();

        foreach ($listResult as $listRow) {

            $dbName = $listRow['Db'];
            if ($dbName != $this->databaseName) {
                continue;
            }

            $name = $listRow['Name'];

            $row = $this->db->query("SHOW CREATE PROCEDURE `$name`")->fetch();

            $createProcedure = $row['Create Procedure'];
            if ($createProcedure === NULL) {
                echo "<span style=\"color: red\">Unavailable: <strong>$name</strong></span><br>\n";
                continue;
            }
            $createProcedure = $createProcedure . ";;";
            $createProcedure = Strings::replace($createProcedure, '/DEFINER=`.+?`\@`.+?`\s?/');

            $dropProcedure = "DROP PROCEDURE IF EXISTS `$name`;;";

            $data = Data::header() . "DELIMITER ;;" . "\n" . "\n" . $dropProcedure . "\n" .
                $createProcedure . "\n" . "\n" .
                "DELIMITER ;" . Data::footer();

            $dump = new Dump($name, Dump::TYPE_PROCEDURE, $this->dumpDir);
            $dump->saveIfChanged($data,
                function ($fromFile) use ($createProcedure) {
                    $createProcedureFromFile = Strings::match($fromFile, "/CREATE.*END;;/s")[0];
                    return Strings::normalize($createProcedureFromFile) == Strings::normalize($createProcedure);
                }
            );
        }
    }

    private function processFunctions()
    {
        $listResult = $this->db->query('SHOW FUNCTION STATUS')->fetchAll();

        foreach ($listResult as $listRow) {

            $dbName = $listRow['Db'];
            if ($dbName != $this->databaseName) {
                continue;
            }

            $name = $listRow['Name'];

            $row = $this->db->query("SHOW CREATE FUNCTION `$name`")->fetch();

            $createFunction = $row['Create Function'];
            if ($createFunction === NULL) {
                echo "<span style=\"color: red\">Unavailable: <strong>$name</strong></span><br>\n";
                continue;
            }
            $createFunction = $createFunction . ";;";
            $createFunction = Strings::replace($createFunction, '/DEFINER=`.+?`\@`.+?`\s?/');

            $dropFunction = "DROP FUNCTION IF EXISTS `$name`;;";

            $data = Data::header() . "DELIMITER ;;" . "\n" . "\n" . $dropFunction . "\n" .
                $createFunction . "\n" . "\n" .
                "DELIMITER ;" . Data::footer();

            $dump = new Dump($name, Dump::TYPE_FUNCTION, $this->dumpDir);
            $dump->saveIfChanged($data,
                function ($fromFile) use ($createFunction) {
                    $createFunctionFromFile = Strings::match($fromFile, "/CREATE.*END;;/s")[0];
                    return Strings::normalize($createFunctionFromFile) == Strings::normalize($createFunction);
                }
            );
        }
    }

    private function processTriggers()
    {
        $triggers = $this->db->query('SHOW TRIGGERS')->fetchAll();

        foreach ($triggers as $row) {
            $name = $row['Trigger'];
            $timing = $row['Timing'];
            $event = $row['Event'];
            $table = $row['Table'];
            $statement = $row['Statement'];

            $createTrigger =
                "CREATE TRIGGER `$name` $timing $event ON `$table` FOR EACH ROW" . "\n" .
                $statement . ";;";
            $dropTrigger = "DROP TRIGGER IF EXISTS `$name`;;";

            $data = Data::header() . "DELIMITER ;;" . "\n" . "\n" . $dropTrigger . "\n" .
                $createTrigger . "\n" . "\n" .
                "DELIMITER ;" . Data::footer();

            $dump = new Dump($name, Dump::TYPE_TRIGGER, $this->dumpDir);
            $dump->saveIfChanged($data,
                function ($fromFile) use ($createTrigger) {
                    $createTriggerFromFile = Strings::match($fromFile, "/CREATE.*END;;/s")[0];
                    return Strings::normalize($createTriggerFromFile) == Strings::normalize($createTrigger);
                }
            );
        }
    }

    private function processTables()
    {
        $tables = $this->db->getConnection()->getSupplementalDriver()->getTables();

        foreach ($tables as $table) {
            if ($table['view'] == FALSE) {

                $tableName = $table['name'];

                // Fetch SHOW CREATE TABLE string
                $row = $this->db->query("SHOW CREATE TABLE `$tableName`")->fetch();
                $createTable = $row["Create Table"];

                // Remove Auto increment value
                $createTable = Strings::replace($createTable, '/AUTO_INCREMENT\s*=\s*\d+\s?/');

                // Add ;
                $createTable .= ";";

                $dropTable = "DROP TABLE IF EXISTS `$tableName`;\n";

                $data = Data::header() . $dropTable . $createTable . "\n" . Data::footer();

                $dump = new Dump($tableName, Dump::TYPE_TABLE, $this->dumpDir);
                $dump->saveIfChanged($data,
                    function ($fromFile) use ($createTable) {
                        $createTableFromFile = Strings::match($fromFile, "/CREATE.*;/s")[0];
                        return Strings::normalize($createTableFromFile) == Strings::normalize($createTable);
                    }
                );
            }
        }

    }

    private function processViews()
    {
        $tables = $this->db->getConnection()->getSupplementalDriver()->getTables();

        foreach ($tables as $table) {
            if ($table['view'] == TRUE) {

                $viewName = $table['name'];

                // Fetch SHOW CREATE VIEW string
                $row = $this->db->query("SHOW CREATE VIEW `$viewName`")->fetch();
                $createView = $row["Create View"];

                // Remove Auto increment value
                $createView = Strings::replace($createView, '#SQL SECURITY DEFINER\s?#i');
                $createView = Strings::replace($createView, '#ALGORITHM=UNDEFINED\s?#i');
                $createView = Strings::replace($createView, '/DEFINER=`.+?`\@`.+?`\s?/');

                // Add ;
                $createView .= ";";

                $dropTable = "DROP VIEW IF EXISTS `$viewName`;\n";

                $data = Data::header() . $dropTable . $createView . "\n" . Data::footer();

                $dump = new Dump($viewName, Dump::TYPE_VIEW, $this->dumpDir);
                $dump->saveIfChanged($data,
                    function ($fromFile) use ($createView) {
                        $createViewFromFile = Strings::match($fromFile, "/CREATE.*;/s")[0];
                        return Strings::normalize($createViewFromFile) == Strings::normalize($createView);
                    }
                );
            }
        }

    }
}
