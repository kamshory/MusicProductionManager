<?php

namespace MagicObject\Util\Database;

use Exception;
use MagicObject\Database\PicoDatabase;
use MagicObject\Database\PicoTableInfo;
use MagicObject\Exceptions\InvalidParameterException;
use MagicObject\MagicObject;
use MagicObject\SecretObject;
use PDO;

/**
 * Class PicoDatabaseUtilSqlite
 *
 * This class extends the PicoDatabaseUtilBase and implements the PicoDatabaseUtilInterface specifically 
 * for SQLite database operations. It provides specialized utility methods tailored to leverage SQLite's 
 * features and syntax while ensuring compatibility with the general database utility interface.
 *
 * Key functionalities include:
 *
 * - **Retrieve and display column information for tables:** Methods to fetch detailed column data, 
 *   including types and constraints, from SQLite tables.
 * - **Generate SQL statements to create tables based on existing structures:** Automated generation 
 *   of CREATE TABLE statements to replicate existing table schemas.
 * - **Dump data from various sources into SQL insert statements:** Convert data from different formats 
 *   into valid SQL INSERT statements for efficient data insertion.
 * - **Facilitate the import of data between source and target databases:** Streamlined processes for 
 *   transferring data, including handling pre and post-import scripts to ensure smooth operations.
 * - **Ensure data integrity by fixing types during the import process:** Validation and correction of 
 *   data types to match SQLite's requirements, enhancing data quality during imports.
 *
 * This class is designed for developers who are working with SQLite databases and need a robust set of tools 
 * to manage database operations efficiently. By adhering to the PicoDatabaseUtilInterface, it provides 
 * a consistent API for database utilities while taking advantage of SQLite-specific features.
 *
 * Usage:
 * To use this class, instantiate it with a SQLite database connection and utilize its methods to perform 
 * various database tasks, ensuring efficient data management and manipulation.
 *
 * @author Kamshory
 * @package MagicObject\Util\Database
 * @link https://github.com/Planetbiru/MagicObject
 */
class PicoDatabaseUtilSqlite extends PicoDatabaseUtilBase implements PicoDatabaseUtilInterface // NOSONAR
{

    /**
     * Generates a SQL CREATE TABLE query based on the provided class annotations.
     *
     * This function inspects the given class for its properties and their annotations
     * to construct a SQL statement that can be used to create a corresponding table in a database.
     * It extracts the table name from the `@Table` annotation and processes each property 
     * to determine the column definitions from the `@Column` annotations.
     *
     * @param MagicObject $entity     The instance of the class whose properties will be used
     *                                to generate the table structure.
     * @param bool $createIfNotExists If true, the query will include an "IF NOT EXISTS" clause.
     * @param bool $dropIfExists      Whether to add "DROP TABLE IF EXISTS" before the CREATE statement (default is false).
     * @return string The generated SQL CREATE TABLE query.
     * 
     * @throws ReflectionException If the class does not exist or is not accessible.
     */
    public function showCreateTable($entity, $createIfNotExists = false, $dropIfExists = false) {        
        $tableInfo = $entity->tableInfo();
        $tableName = $tableInfo->getTableName();
    
        // Start building the CREATE TABLE query
        $condition = $this->createIfNotExists($createIfNotExists);

        $autoIncrementKeys = $this->getAutoIncrementKey($tableInfo);

        $query = "";
        if($dropIfExists)
        {
            $query .= "-- DROP TABLE IF EXISTS $tableName;\r\n\r\n";
        }
        $query .= "CREATE TABLE$condition $tableName (\n";
    
        // Define primary key
        $primaryKey = null;

        $pKeys = $tableInfo->getPrimaryKeys();

        $pKeyArr = [];
        $pKeyArrUsed = [];
        if(isset($pKeys) && is_array($pKeys) && !empty($pKeys))
        {
            $pkVals = array_values($pKeys);
            foreach($pkVals as $pk)
            {
                $pKeyArr[] = $pk['name'];
            }
        }

        foreach ($tableInfo->getColumns() as $column) {
        
            $columnName = $column['name'];
            $columnType = $column['type'];
            $length = isset($column['length']) ? $column['length'] : null;
            $nullable = (isset($column['nullable']) && $column['nullable'] === 'true') ? ' NULL' : ' NOT NULL';
            $defaultValue = isset($column['default_value']) ? " DEFAULT '{$column['default_value']}'" : '';

            // Convert column type for SQL
            $columnType = strtolower($columnType); // Convert to lowercase for case-insensitive comparison

            $attr = $this->determineSqlType($column, $autoIncrementKeys, $length, $pKeyArrUsed);
            
            
            $sqlType = $attr['sqlType'];
            $pKeyArrUsed = $attr['pKeyArrUsed'];
            
            // Add to query
            $query .= "\t$columnName $sqlType$nullable$defaultValue,\n";
            
        }
    
        // Remove the last comma and add primary key constraint
        $query = rtrim($query, ",\n") . "\n";
    
        $pKeyArrFinal = $this->getPkeyArrayFinal($pKeyArr, $pKeyArrUsed);

        if (!empty($pKeyArrFinal)) {
            $primaryKey = implode(", ", $pKeyArrFinal);
            $query = rtrim($query, ",\n");
            $query .= ",\n\tPRIMARY KEY ($primaryKey)\n";
        }
    
        $query .= ");";
    
        return str_replace("\n", "\r\n", $query);
    }
    
    /**
     * Returns "IF NOT EXISTS" if specified, otherwise an empty string.
     *
     * @param bool $createIfNotExists Flag indicating whether to include "IF NOT EXISTS".
     * @return string The "IF NOT EXISTS" clause if applicable.
     */
    private function createIfNotExists($createIfNotExists) {
        return $createIfNotExists ? " IF NOT EXISTS" : "";
    }
    
    /**
     * Filter the primary key array to exclude used primary keys.
     *
     * @param array $pKeyArr Array of primary key names.
     * @param array $pKeyArrUsed Array of used primary key names.
     * @return array Filtered array of primary key names.
     */
    private function getPkeyArrayFinal($pKeyArr, $pKeyArrUsed)
    {
        $pKeyArrFinal = [];
        foreach($pKeyArr as $v)
        {
            if(!in_array($v, $pKeyArrUsed))
            {
                $pKeyArrFinal[] = $v;
            }
        }
        return $pKeyArrFinal;
    }
    
    /**
     * Determine the SQL data type based on the given column information and auto-increment keys.
     *
     * @param array $column The column information, expected to include the column name and type.
     * @param array|null $autoIncrementKeys The array of auto-increment keys, if any.
     * @param int $length The length for VARCHAR types.
     * @param array $pKeyArrUsed The array to store used primary key names.
     * @return array An array containing the determined SQL data type and the updated primary key array.
     */
    private function determineSqlType($column, $autoIncrementKeys = null, $length = 255, $pKeyArrUsed = [])
    {
        $columnName = $column[parent::KEY_NAME];
        $columnType = strtolower($column['type']); // Assuming 'type' holds the column type
        $sqlType = '';

        // Check for auto-increment primary key
        if (is_array($autoIncrementKeys) && in_array($columnName, $autoIncrementKeys)) {
            $sqlType = 'INTEGER PRIMARY KEY AUTOINCREMENT';
            $pKeyArrUsed[] = $columnName; // Add to used primary keys
        } else {
            // Default mapping of column types to SQL types
            $typeMapping = array(
                'varchar' => "VARCHAR($length)",
                'tinyint(1)' => 'TINYINT(1)',
                'float' => 'FLOAT',
                'text' => 'TEXT',
                'longtext' => 'LONGTEXT',
                'date' => 'DATE',
                'timestamp' => 'TIMESTAMP',
                'blob' => 'BLOB',
            );

            // Check if the column type exists in the mapping
            if (array_key_exists($columnType, $typeMapping)) {
                $sqlType = $typeMapping[$columnType];
            } else {
                $sqlType = strtoupper($columnType);
                if ($sqlType !== 'TINYINT(1)' && $sqlType !== 'FLOAT' && $sqlType !== 'TEXT' && 
                    $sqlType !== 'LONGTEXT' && $sqlType !== 'DATE' && $sqlType !== 'TIMESTAMP' && 
                    $sqlType !== 'BLOB') 
                {
                    $sqlType = 'VARCHAR(255)'; // Fallback type for unknown types
                }
            }
        }

        return array('sqlType' => $sqlType, 'pKeyArrUsed' => $pKeyArrUsed);
    }


    /**
     * Retrieves a list of columns for a specified table in the database.
     *
     * This method queries the information schema to obtain details about the columns 
     * of the specified table, including their names, data types, nullability, 
     * default values, and any additional attributes such as primary keys and auto-increment.
     *
     * @param PicoDatabase $database The database connection instance.
     * @param string $tableName The name of the table to retrieve column information from.
     * @return array An array of associative arrays containing details about each column,
     *               where each associative array includes:
     *               - 'Field': The name of the column.
     *               - 'Type': The data type of the column.
     *               - 'Null': Indicates if the column allows NULL values ('YES' or 'NO').
     *               - 'Key': Indicates if the column is a primary key ('PRI' or null).
     *               - 'Default': The default value of the column, or 'None' if not set.
     *               - 'Extra': Additional attributes of the column, such as 'auto_increment'.
     * @throws Exception If the database connection fails or the query cannot be executed.
     */
    public function getColumnList($database, $tableName)
    {
        $stmt = $database->query("PRAGMA table_info($tableName)");

        // Fetch and display the column details
        $rows = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = array(
                "Field" => $row['name'],
                "Type" => $row['type'],
                "Null" => $row['notnull'] ? 'YES' : 'NO',
                "Key" => $row['pk'] ? 'PRI' : null,
                "Default" => $row['dflt_value'] ? $row['dflt_value'] : 'None',
                "Extra" => ($row['pk'] == 1 && $row['type'] === 'INTEGER') ? 'auto_increment' : null
            );
        }
        return $rows;
    }

    /**
     * Dumps the structure of a table as a SQL statement.
     *
     * This method generates a SQL CREATE TABLE statement based on the provided table information,
     * including the option to include or exclude specific clauses such as "IF NOT EXISTS" and 
     * "DROP TABLE IF EXISTS". It also handles the definition of primary keys if present.
     *
     * @param PicoTableInfo $tableInfo         The information about the table, including column details and primary keys.
     * @param string        $tableName         The name of the table for which the structure is being generated.
     * @param bool          $createIfNotExists Whether to add "IF NOT EXISTS" in the CREATE statement (default is false).
     * @param bool          $dropIfExists      Whether to add "DROP TABLE IF EXISTS" before the CREATE statement (default is false).
     * @param string|null   $engine            The storage engine to use for the table (optional, default is null).
     * @param string|null   $charset           The character set to use for the table (optional, default is null).
     * @return string                          The SQL statement to create the table, including column definitions and primary keys.
     */
    public function dumpStructure($tableInfo, $tableName, $createIfNotExists = false, $dropIfExists = false, $engine = 'InnoDB', $charset = 'utf8mb4')
    {
        $query = [];
        $columns = [];
        if($dropIfExists)
        {
            $query[] = "-- DROP TABLE IF EXISTS $tableName;";
            $query[] = "";
        }
        $createStatement = "";

        $createStatement = "CREATE TABLE";
        if($createIfNotExists)
        {
            $createStatement .= " IF NOT EXISTS";
        }

        $autoIncrementKeys = $this->getAutoIncrementKey($tableInfo);

        $query[] = "$createStatement $tableName (";

        foreach($tableInfo->getColumns() as $column)
        {
            $columns[] = $this->createColumn($column);
        }
        $query[] = implode(",\r\n", $columns);
        $query[] = ") ";

        $pk = $tableInfo->getPrimaryKeys();
        if(isset($pk) && is_array($pk) && !empty($pk))
        {
            $query[] = "";
            $query[] = "ALTER TABLE $tableName";
            foreach($pk as $primaryKey)
            {
                $query[] = "\tADD PRIMARY KEY ($primaryKey[name])";
            }
            $query[] = ";";
        }

        foreach($tableInfo->getColumns() as $column)
        {
            if(isset($autoIncrementKeys) && is_array($autoIncrementKeys) && in_array($column[parent::KEY_NAME], $autoIncrementKeys))
            {
                $query[] = "";
                $query[] = "ALTER TABLE $tableName \r\n\tMODIFY ".trim($this->createColumn($column), " \r\n\t ")." AUTO_INCREMENT";
                $query[] = ";";
            }
        }

        return implode("\r\n", $query);
    }

    /**
     * Creates a column definition for a SQL statement.
     *
     * This method constructs a SQL column definition based on the provided column details,
     * including the column name, data type, nullability, and default value. The resulting 
     * definition is formatted for use in a CREATE TABLE statement.
     *
     * @param array $column An associative array containing details about the column:
     *                      - string name: The name of the column.
     *                      - string type: The data type of the column (e.g., VARCHAR, INT).
     *                      - bool|string nullable: Indicates if the column allows NULL values (true or 'true' for NULL; otherwise, NOT NULL).
     *                      - mixed default_value: The default value for the column (optional).
     *
     * @return string The SQL column definition formatted as a string, suitable for inclusion in a CREATE TABLE statement.
     */
    public function createColumn($column)
    {
        $col = [];
        $col[] = "\t";
        $col[] = "".$column[parent::KEY_NAME]."";
        $col[] = $column['type'];
        if(isset($column['nullable']) && strtolower(trim($column['nullable'])) == 'true')
        {
            $col[] = "NULL";
        }
        else
        {
            $col[] = "NOT NULL";
        }
        if(isset($column['default_value']))
        {
            $defaultValue = $column['default_value'];
            $defaultValue = $this->fixDefaultValue($defaultValue, $column['type']);
            $col[] = "DEFAULT $defaultValue";
        }
        return implode(" ", $col);
    }

    /**
     * Fixes the default value for SQL insertion based on its type.
     *
     * This method processes the given default value according to the specified data type,
     * ensuring that it is correctly formatted for SQL insertion. For string-like types,
     * the value is enclosed in single quotes, while boolean and null values are returned 
     * as is.
     *
     * @param mixed $defaultValue The default value to fix, which can be a string, boolean, or null.
     * @param string $type The data type of the column (e.g., ENUM, CHAR, TEXT, INT, FLOAT, DOUBLE).
     *
     * @return mixed The fixed default value formatted appropriately for SQL insertion.
     */
    public function fixDefaultValue($defaultValue, $type)
    {
        if(strtolower($defaultValue) == 'true' 
        || strtolower($defaultValue) == 'false' 
        || strtolower($defaultValue) == 'null'
        )
        {
            return $defaultValue;
        }
        if(stripos($type, 'enum') !== false 
        || stripos($type, 'char') !== false 
        || stripos($type, 'text') !== false 
        || stripos($type, 'int') !== false 
        || stripos($type, 'float') !== false 
        || stripos($type, 'double') !== false
        )
        {
            return "'".$defaultValue."'";
        }
        return $defaultValue;
    }

    /**
     * Fixes imported data based on specified column types.
     *
     * This method processes the input data array and adjusts the values 
     * according to the expected types defined in the columns array. It 
     * supports boolean, integer, and float types.
     *
     * @param mixed[] $data The input data to be processed.
     * @param string[] $columns An associative array mapping column names to their types.
     * @return mixed[] The updated data array with fixed types.
     */
    public function fixImportData($data, $columns)
    {
        // Iterate through each item in the data array
        foreach($data as $name=>$value)
        {
            // Check if the column exists in the columns array
            if(isset($columns[$name]))
            {
                $type = $columns[$name];
                
                if(strtolower($type) == 'tinyint(1)' 
                || strtolower($type) == 'boolean' 
                || strtolower($type) == 'bool'
                )
                {
                    // Process boolean types
                    $data = $this->fixBooleanData($data, $name, $value);
                }
                else if(stripos($type, 'integer') !== false 
                || stripos($type, 'int(') !== false
                )
                {
                    // Process integer types
                    $data = $this->fixIntegerData($data, $name, $value);
                }
                else if(stripos($type, 'float') !== false 
                || stripos($type, 'double') !== false 
                || stripos($type, 'decimal') !== false
                )
                {
                    // Process float types
                    $data = $this->fixFloatData($data, $name, $value);
                }
            }
        }
        return $data;
    }

    /**
     * Automatically configures the import data settings based on the source and target databases.
     *
     * This method connects to the source and target databases, retrieves the list of existing 
     * tables, and updates the configuration for each target table by checking its presence in the 
     * source database. It handles exceptions and logs any errors encountered during the process.
     *
     * @param SecretObject $config The configuration object containing database and table information.
     * @return SecretObject The updated configuration object with modified table settings.
     */
    public function autoConfigureImportData($config)
    {
        $databaseConfigSource = $config->getDatabaseSource();
        $databaseConfigTarget = $config->getDatabaseTarget();

        $databaseSource = new PicoDatabase($databaseConfigSource);
        $databaseTarget = new PicoDatabase($databaseConfigTarget);
        try
        {
            $databaseSource->connect();
            $databaseTarget->connect();
            $tables = $config->getTable();

            $existingTables = [];
            foreach($tables as $tb)
            {
                $existingTables[] = $tb->getTarget();
            }

            $sourceTableList = $databaseSource->fetchAll("SELECT name FROM sqlite_master WHERE type='table'", PDO::FETCH_NUM);
            $targetTableList = $databaseTarget->fetchAll("SELECT name FROM sqlite_master WHERE type='table'", PDO::FETCH_NUM);

            $sourceTables = call_user_func_array('array_merge', $sourceTableList);
            $targetTables = call_user_func_array('array_merge', $targetTableList);

            foreach($targetTables as $target)
            {
                $tables = $this->updateConfigTable($databaseSource, $databaseTarget, $tables, $sourceTables, $target, $existingTables);
            }
            $config->setTable($tables);
        }
        catch(Exception $e)
        {
            error_log($e->getMessage());
        }
        return $config;
    }
    
    /**
     * Check if a table exists in the database.
     *
     * This method queries the database to determine if a specified table exists by checking 
     * the SQLite master table. It throws an exception if the table name is null or empty.
     *
     * @param PicoDatabase $database The database instance to check.
     * @param string $tableName The name of the table to check.
     * @return bool True if the table exists, false otherwise.
     * @throws InvalidParameterException If the table name is null or empty.
     */
    public function tableExists($database, $tableName)
    {
        if(!isset($tableName) || empty($tableName))
        {
            throw new InvalidParameterException("Table name can't be null or empty.");
        }
        $query = "SELECT name FROM sqlite_master WHERE type='table' AND name=:tableName";
        $stmt = $database->getDatabaseConnection()->prepare($query);
        $stmt->bindValue(':tableName', $tableName);
        $stmt->execute();
        return $stmt->fetch() !== false;
    }
    
}