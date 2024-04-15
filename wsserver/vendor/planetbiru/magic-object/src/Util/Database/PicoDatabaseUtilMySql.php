<?php

namespace MagicObject\Util\Database;

use MagicObject\Database\PicoDatabase;

class PicoDatabaseUtilMySql
{
    /**
     * Get column list
     *
     * @param PicoDatabase $database
     * @param string $picoTableName
     * @return array
     */
    public static function getColumnList($database, $picoTableName)
    {
        $sql = "SHOW COLUMNS FROM $picoTableName";
        return $database->fetchAll($sql);
    }
}