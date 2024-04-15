<?php

namespace MagicObject\Geneator;

use MagicObject\Database\PicoDatabase;
use MagicObject\Database\PicoDatabaseType;
use MagicObject\Util\Database\PicoDatabaseUtilMySql;

class PicoColumnGenerator
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
        if($database->getDatabaseType() == PicoDatabaseType::DATABASE_TYPE_MARIADB || $database->getDatabaseType() == PicoDatabaseType::DATABASE_TYPE_MYSQL)
        {
            return PicoDatabaseUtilMySql::getColumnList($database, $picoTableName);
        }
        else
        {
            return array();
        }
    }
}