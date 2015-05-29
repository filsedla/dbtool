<?php

/**
 * Copyright (c) 2014 Filip Sedláček (filsedla@gmail.com)
 */

namespace Filsedla\DbTool;

use Nette\Utils\Callback;

final class Dump
{
    const TYPE_TABLE = 1;
    const TYPE_TRIGGER = 2;
    const TYPE_PROCEDURE = 3;
    const TYPE_VIEW = 4;

    /** @var int */
    private $type;

    /** @var string */
    private $name;

    /** @var string */
    private $dumpDirectory;

    /**
     * @param string $name
     * @param int $type
     * @param $dumpDirectory
     */
    function __construct($name, $type, $dumpDirectory)
    {
        switch ($type) {
            case self::TYPE_TABLE:
                $dumpSubdirectory = '/tables';
                break;
            case self::TYPE_TRIGGER:
                $dumpSubdirectory = '/triggers';
                break;
            case self::TYPE_PROCEDURE:
                $dumpSubdirectory = '/procedures';
                break;
            case self::TYPE_VIEW:
                $dumpSubdirectory = '/views';
                break;
            default:
                $dumpSubdirectory = '';
                break;
        };
        $this->dumpDirectory = $dumpDirectory . $dumpSubdirectory;
        if (!is_dir($this->dumpDirectory)) {
            mkdir($this->dumpDirectory);
        }
        $this->name = $name;
        $this->type = $type;
    }


    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->dumpDirectory . "/" . $this->name . ".sql";
    }

    /**
     * @return string
     */
    public function load()
    {
        return file_get_contents($this->getFilename());
    }

    /**
     * @param $data
     * @return int
     */
    public function save($data)
    {
        return file_put_contents($this->getFilename(), $data);
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return is_file($this->getFilename());
    }


    /**
     * @param $data
     * @param callable $compareCallback
     */
    public function saveIfChanged($data, $compareCallback)
    {
        // The file exists
        if ($this->exists()) {
            $fromFile = $this->load();
            $same = Callback::invoke($compareCallback, $fromFile);

            // And the create statement is the same
            if ($same) {
                echo "No change: $this->name<br>\n"; // then do not rewrite this file

            } else {
                echo "Updating: <strong>$this->name</strong><br>\n";
                $this->save($data);
            }

        } else {
            echo "New: <strong>$this->name</strong><br>\n";
            $this->save($data);
        }
    }
}