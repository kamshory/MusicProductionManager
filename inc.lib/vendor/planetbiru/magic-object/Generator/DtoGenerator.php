<?php

namespace MagicObject\Geneator;

use MagicObject\Database\PicoDatabase;
use MagicObject\Util\StringUtil;

class EntityGenerator
{
    /**
     * Database
     *
     * @var PicoDatabase
     */
    private $database;
    private $baseDir = "";
    private $baseNamespace = "";
    private $tableName = "";
    
    /**
     * Constructor
     *
     * @param PicoDatabase $database
     * @param string $baseDir
     * @param string $baseNamespace
     * @param string $tableName
     */
    public function __construct($database, $baseDir, $baseNamespace, $tableName)
    {
        $this->database = $database;
        $this->baseDir = $baseDir;
        $this->baseNamespace = $baseNamespace;
        $this->tableName = $tableName;
    }
    
    /**
     * Create property
     *
     * @param array $typeMap
     * @param string $columnName
     * @param string $columnType
     * @return string
     */
    private function createProperty($typeMap, $columnName, $columnType)
    {
        $propertyName = StringUtil::camelize($columnName);
        $docs = array();
        $docStart = "\t/**";
        $docEnd = "\t */";

        $docs[] = $docStart;
        $docs[] = "\t * ".$this->getPropertyName($columnName);
        $docs[] = "\t * ";

        $type = $this->getDataType($typeMap, $columnType);

        $docs[] = "\t * @var $type";
        $docs[] = $docEnd;
        $prop = "\tprotected \$$propertyName;";
        return implode("\r\n", $docs)."\r\n".$prop."\r\n";
    }

    private function getPropertyName($name)
    {
        $arr = explode("_", $name);
        foreach($arr as $k => $v)
        {
            $arr[$k] = ucfirst($v);
            $arr[$k] = str_replace("Id","ID", $arr[$k]);
            $arr[$k] = str_replace("Ip","IP", $arr[$k]);
        }
        return implode(" ", $arr);
    }

    /**
     * Get data type
     *
     * @param array $typeMap
     * @param string $columnType
     * @return string
     */
    private function getDataType($typeMap, $columnType)
    {
        $type = "";
        foreach($typeMap as $key=>$val)
        {
            if(stripos($columnType, $key) === 0)
            {
                $type = $val;
                break;
            }
        }
        if(empty($type))
        {
            $type = "string";
        }
        return $type;
    }

    /**
     * Value of
     *
     * @param string $picoTableName
     * @param array $rows
     * @return string
     */
    private function createValuueOf($picoTableName, $rows)
    {
        $className = ucfirst(StringUtil::camelize($picoTableName));
        $str = "";
        $str .="    /**\r\n";
        $str .="     * Construct $className"."Dto from $className and not copy other properties\r\n";
        $str .="     * \r\n";
        $str .="     * @param $className \$input\r\n";
        $str .="     * @return self\r\n";
        $str .="     */\r\n";
        $str .="    public static function valueOf(\$input)\r\n";
        $str .="    {\r\n";
        $str .="        \$output = new $className"."Dto();\r\n";

        foreach($rows as $row)
        {
            $columnName = $row['Field'];
            $str .="        \$output->set".ucfirst(StringUtil::camelize($columnName))."(\$input->get".ucfirst(StringUtil::camelize($columnName))."());\r\n";
        }
        $str .="        return \$output;\r\n";
        $str .="    }\r\n";
        return $str;
    }
    
    /**
     * Get type map
     *
     * @return array
     */
    private function getTypeMap()
    {
        return array(
            "double" => "double",
            "float" => "double",
            "bigint" => "integer",
            "smallint" => "integer",
            "tinyint(1)" => "bool",
            "tinyint" => "integer",
            "int" => "integer",
            "varchar" => "string",
            "char" => "string",
            "tinytext" => "string",
            "mediumtext" => "string",
            "longtext" => "string",
            "text" => "string",
            "enum" => "string",
            "boolean" => "bool",
            "bool" => "bool",
            "timestamp" => "string",
            "datetime" => "string",
            "date" => "string",
            "time" => "string"
        );    
    }

    
    public function generate()
    {
        $typeMap = $this->getTypeMap();
        $picoTableName = $this->tableName;
        $className = ucfirst(StringUtil::camelize($picoTableName));
        $fileName = $this->baseNamespace."/".$className;
        $path = __DIR__ . "/inc.lib/classes/".$fileName."Dto.php";
        $path = str_replace("\\", "/", $path);

        $sql = "SHOW COLUMNS FROM $picoTableName";


        $rows = $this->database->fetchAll($sql);

            $attrs = [];
            if(is_array($rows))
            {
                foreach($rows as $row)
                {
                    $columnName = $row['Field'];
                    $columnType = $row['Type'];

                    $prop = $this->createProperty($typeMap, $columnName, $columnType);
                    $attrs[] = $prop;
                }
                $valueOf = $this->createValuueOf($picoTableName, $rows);
                $attrs[] = $valueOf;
            }


        $uses = array();
        $uses[] = "";

        $classStr = '<?php

namespace '.$this->baseNamespace.';

use MagicObject\\SetterGetter;
use MusicProductionManager\\Data\\Entity\\'.$className.';

/**
 * @JSON(property-naming-strategy=SNAKE_CASE)
 */
class '.$className.'Dto extends SetterGetter
{
'.implode("\r\n", $attrs).'
}';
        return file_put_contents($path, $classStr);
    }
}