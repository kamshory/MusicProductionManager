<?php

namespace MagicObject\Generator;

use MagicObject\Database\PicoDatabase;
use MagicObject\Util\PicoStringUtil;

class PicoDtoGenerator
{
    /**
     * Database
     *
     * @var PicoDatabase
     */
    private $database;
    /**
     * Base directory
     *
     * @var string
     */
    private $baseDir = "";

    /**
     * Base namespace
     *
     * @var string
     */
    private $baseNamespace = "";

    /**
     * Table name
     *
     * @var string
     */
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
        $tableName = str_replace(array('"', "'"), "", $tableName);
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
        $propertyName = PicoStringUtil::camelize($columnName);
        $docs = array();
        $docStart = "\t/**";
        $docEnd = "\t */";

        $description = $this->getPropertyName($columnName);
        $type = $this->getDataType($typeMap, $columnType);

        $docs[] = $docStart;
        $docs[] = "\t * ".$description;
        $docs[] = "\t * ";
        $docs[] = "\t * @Label(content=\"$description\")";
        $docs[] = "\t * @var $type";
        $docs[] = $docEnd;
        $prop = "\tprotected \$$propertyName;";
        return implode("\r\n", $docs)."\r\n".$prop."\r\n";
    }

    /**
     * Get property name
     *
     * @param string $name
     * @return string
     */
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
    private function createValueOf($picoTableName, $rows)
    {
        $className = ucfirst(PicoStringUtil::camelize($picoTableName));
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
            $str .="        \$output->set".ucfirst(PicoStringUtil::camelize($columnName))."(\$input->get".ucfirst(PicoStringUtil::camelize($columnName))."());\r\n";
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
            "tinyint(1)" => "boolean",
            "tinyint" => "integer",
            "int" => "integer",
            "varchar" => "string",
            "char" => "string",
            "tinytext" => "string",
            "mediumtext" => "string",
            "longtext" => "string",
            "text" => "string",
            "enum" => "string",
            "bool" => "boolean",
            "boolean" => "boolean",
            "timestamp" => "string",
            "datetime" => "string",
            "date" => "string",
            "time" => "string"
        );    
    }

    /**
     * Generate DTO
     *
     * @return string
     */
    public function generate()
    {
        $typeMap = $this->getTypeMap();
        $picoTableName = $this->tableName;
        $className = ucfirst(PicoStringUtil::camelize($picoTableName));
        $fileName = $this->baseNamespace."/".$className;
        $path = $this->baseDir."/".$fileName."Dto.php";
        $path = str_replace("\\", "/", $path);

        $rows = PicoColumnGenerator::getColumnList($this->database, $picoTableName);
        
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
            $prop = $this->createValueOf($picoTableName, $rows);
            $attrs[] = $prop;
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