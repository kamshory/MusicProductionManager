<?php


use MusicProductionManager\Util\StringUtil;

require_once __DIR__ . "/inc/app.php";

$picoTableName = "";

if($argc > 1)
{
    $picoTableName = trim($argv[1]);
}
else
{
    if(isset($_GET['tablename']))
    {
        $picoTableName = trim($_GET['tablename']);
    }
}

if(empty($picoTableName))
{
    exit();
}

$className = ucfirst(StringUtil::camelize($picoTableName));
$namespace = "MusicProductionManager\\Data\\Dto";

$fileName = $namespace."/".$className;
$path = __DIR__ . "/inc.lib/classes/".$fileName.".php";
$path = str_replace("\\", "/", $path);

$sql = "SHOW COLUMNS FROM $picoTableName";

$typeMap = array(
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
function createProperty($typeMap, $columnName, $columnType, $columnKey, $columnNull, $columnDefault, $columnExtra)
{
    $propertyName = StringUtil::camelize($columnName);
    $docs = array();
    $docStart = "\t/**";
    $docEnd = "\t */";

    $docs[] = $docStart;
    $docs[] = "\t * ".getPropertyName($columnName);
    $docs[] = "\t * ";

    $type = getDataType($typeMap, $columnType);

    $docs[] = "\t * @var $type";
    $docs[] = $docEnd;
    $prop = "\tprotected \$$propertyName;";
    return implode("\r\n", $docs)."\r\n".$prop."\r\n";
}

function getPropertyName($name)
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
function getDataType($typeMap, $columnType)
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
function getDataLength($str)
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
 * Value of
 *
 * @param string $picoTableName
 * @param array $rows
 * @return string
 */
function createValuueOf($picoTableName, $rows)
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
        $str .="        \$output->set".ucfirst(StringUtil::camelize($columnName))."(\$input->get".ucfirst(StringUtil::camelize($columnName)).");\r\n";
    }
    $str .="        return \$output;\r\n";
    $str .="    }\r\n";
    $str .="\r\n";    
    $str .="}\r\n";
    return $str;
}
$rows = $database->fetchAll($sql);

$attrs = [];
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

        $prop = createProperty($typeMap, $columnName, $columnType, $columnKey, $columnNull, $columnDefault, $columnExtra);
        $attrs[] = $prop;
    }
    $valueOf = createValuueOf($picoTableName, $rows);
    $attrs[] = $valueOf;
}

$uses = array();
$uses[] = "";

$classStr = '<?php

namespace '.$namespace.';

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
$written = file_put_contents($path, $classStr);
echo "PATH = $path<br>\r\n";
echo "WRITTEN = $written<br>\r\n";
echo "SELESAI<br>\r\n";

