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

        echo "-------------- <em>Processing triggers</em> --------------<br>\n";
        $this->processTriggers();

        echo "-------------- <em>Processing procedures</em> --------------<br>\n";
        $this->processProcedures();

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

            $createProcedure = $row['Create Procedure'] . ";;";
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
}
