<?php

namespace MagicObject\Generator;

use MagicObject\Database\PicoDatabase;
use MagicObject\Util\PicoStringUtil;

/**
 * PicoEntityGenerator is an entity generator for automatically generating PHP code. 
 * This class is optimized on the MariaDB database. 
 * Users must provide appropriate parameters so that the entity class can be directly used in the application.
 */
class PicoEntityGenerator
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
     * @param string $columnKey
     * @param string $columnNull
     * @param string $columnDefault
     * @param string $columnExtra
     * @return string
     */
    private function createProperty($typeMap, $columnName, $columnType, $columnKey, $columnNull, $columnDefault, $columnExtra)
    {
        $propertyName = PicoStringUtil::camelize($columnName);
        $description = $this->getPropertyName($columnName);
        $type = $this->getDataType($typeMap, $columnType);

        $docs = array();
        $docStart = "\t/**";
        $docEnd = "\t */";

        $docs[] = $docStart;
        $docs[] = "\t * ".$description;
        $docs[] = "\t * ";

        if(!empty($columnKey) && stripos($columnKey, "PRI") === 0)
        {
            $docs[] = "\t * @Id";
            if(stripos("auto_increment", $columnExtra) === false)
            {
                $docs[] = "\t * @GeneratedValue(strategy=GenerationType.UUID)";
            }
        }

        if(stripos("auto_increment", $columnExtra) !== false)
        {
            $docs[] = "\t * @GeneratedValue(strategy=GenerationType.IDENTITY)";
        }
        
        if(strcasecmp($columnNull, 'No') == 0)
        {
            $docs[] = "\t * @NotNull";
        }

        $attrs = array();
        $attrs[] = "name=\"$columnName\"";
        $attrs[] = "type=\"$columnType\"";
        $length = $this->getDataLength($columnType);
        if($length > 0)
        {
            $attrs[] = "length=$length";
        }

        if(!empty($columnDefault))
        {
            $attrs[] = "default_value=\"".$columnDefault."\"";
        }
        if(!empty($columnNull))
        {
            $val = stripos($columnNull, "YES") === 0 ? "true" : "false";
            $attrs[] = "nullable=$val";
        }

        if($columnName == "time_create" || $columnName == "admin_create" || $columnName == "ip_create")
        {
            $attrs[] = "updatable=false";
        }

        if(!empty($columnExtra))
        {
            $attrs[] = "extra=\"".$columnExtra."\"";
        }

        $docs[] = "\t * @Column(".implode(", ", $attrs).")";
        if(!empty($columnDefault))
        {
            $docs[] = "\t * @DefaultColumn(value=\"".$columnDefault."\")";        
        }


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
     * Get column length
     *
     * @param string $str
     * @return integer
     */
    private function getDataLength($str)
    {
        $str2 = preg_replace('~\D~', '', $str);
        if(empty($str2))
        {
            $str2 = "0";
        }
        $length = $str2 * 1;
        
        if(stripos($str, "datetime") !== false || stripos($str, "timestamp") !== false)
        {
            $length += 20;
            if($length == 20)
            {
                $length = 19;
            }
        }
        return $length;
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
     * Generate entity
     *
     * @return string
     */
    public function generate()
    {
        $typeMap = $this->getTypeMap();
        $picoTableName = $this->tableName;

        $className = ucfirst(PicoStringUtil::camelize($picoTableName));
        $fileName = $this->baseNamespace."/".$className;
        $path = $this->baseDir."/".$fileName.".php";
        $path = str_replace("\\", "/", $path);
        
        $dir = dirname($path);
        mkdir($dir, 0755, true);

        $rows = PicoColumnGenerator::getColumnList($this->database, $picoTableName);

        $attrs = array();
        if(is_array($rows))
        {
            foreach($rows as $row)
            {
                $columnName = $row['Field'];
                $columnType = $row['Type'];
                $columnKey = $row['Key'];
                $columnNull = $row['Null'];
                $columnDefault = $row['Default'];
                $columnExtra = $row['Extra'];

                $prop = $this->createProperty($typeMap, $columnName, $columnType, $columnKey, $columnNull, $columnDefault, $columnExtra);
                $attrs[] = $prop;
            }
        }

        $uses = array();
        $uses[] = "";

        $classStr = '<?php

namespace '.$this->baseNamespace.';

use MagicObject\MagicObject;'.implode("\r\n", $uses).'

/**
 * @Entity
 * @JSON(property-naming-strategy=SNAKE_CASE)
 * @Table(name="'.$picoTableName.'")
 */
class '.$className.' extends MagicObject
{
'.implode("\r\n", $attrs).'
}';
        return file_put_contents($path, $classStr);
    }
}