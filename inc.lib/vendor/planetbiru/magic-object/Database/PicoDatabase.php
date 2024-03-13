<?php

namespace MagicObject\Database;

use PDO;
use PDOException;
use PDOStatement;
use MagicObject\Constants\PicoConstants;
use MagicObject\Exceptions\NullPointerException;

class PicoDatabase
{
	const QUERY_INSERT = "insert";
	const QUERY_UPDATE = "update";
	const QUERY_DELETE = "delete";
	const QUERY_TRANSACTION = "transaction";

	/**
	 * Database credential
	 *
	 * @var PicoDatabaseCredentials
	 */
	private $databaseCredentials;

	/**
	 * Indicate that database is connected or not
	 *
	 * @var boolean
	 */
	private $connected = false;

	/**
	 * Database connection
	 *
	 * @var PDO
	 */
	private $databaseConnection;

	/**
	 * Database type
	 * @var string
	 */
	private $databaseType = "";

	private $callbackExecuteQuery = null;
	private $callbackDebugQuery = null;

	/**
	 * Constructor
	 * @param PicoDatabaseCredentials $databaseCredentials
	 */
	public function __construct($databaseCredentials, $callbackExecuteQuery = null, $callbackDebugQuery = null) //NOSONAR
	{
		$this->databaseCredentials = $databaseCredentials;
		if($callbackExecuteQuery != null && is_callable($callbackExecuteQuery))
		{
			$this->callbackExecuteQuery = $callbackExecuteQuery;
		}
		if($callbackDebugQuery != null && is_callable($callbackDebugQuery))
		{
			$this->callbackDebugQuery = $callbackDebugQuery;
		}
	}

	/**
	 * Connect to database
	 * @return bool true if success and false if failed
	 */
	public function connect()
	{
		date_default_timezone_set($this->databaseCredentials->getTimeZone());
		$timezoneOffset = date("P");
		$connected = true;
		try 
		{
			$connectionString = $this->databaseCredentials->getDriver() . ':host=' . $this->databaseCredentials->getHost() . '; port=' . $this->databaseCredentials->getPort() . '; dbname=' . $this->databaseCredentials->getDatabaseName();
			$this->databaseType = $this->databaseCredentials->getDriver();
			$this->databaseConnection = new PDO(
				$connectionString,
				$this->databaseCredentials->getUsername(),
				$this->databaseCredentials->getPassword(),
				array(
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '$timezoneOffset';",
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					PDO::MYSQL_ATTR_FOUND_ROWS => true
				)
			);

			$connected = true;
			$this->connected = $connected;
		} 
		catch (PDOException $e) 
		{
			// Do nothing
		}
		return $connected;
	}

	/**
	 * Get database connection
	 * @return PDO Represents a connection between PHP and a database server.
	 */
	public function getDatabaseConnection()
	{
		return $this->databaseConnection;
	}

	/**
	 * Fetch result
	 *
	 * @param string $sql
	 * @param integer $tentativeType
	 * @param array $defaultValue
	 * @return array|null
	 */
	public function fetch($sql, $tentativeType = PDO::FETCH_ASSOC, $defaultValue = null)
	{
		if($this->databaseConnection == null)
		{
			throw new NullPointerException(PicoConstants::DATABASE_NONECTION_IS_NULL);
		}
		$result = array();
		$this->executeDebug($sql);
		$stmt = $this->databaseConnection->prepare($sql);
		try 
		{
			$stmt->execute();
			if ($stmt->rowCount() > 0) 
			{
				$result = $stmt->fetch($tentativeType);
			} 
			else 
			{
				$result = $defaultValue;
			}
		} 
		catch (PDOException $e) 
		{
			$result = $defaultValue;
		}
		return $result;
	}

	/**
	 * Check if record is exists
	 *
	 * @param string $sql
	 * @return bool
	 */
	public function isRecordExists($sql)
	{
		if($this->databaseConnection == null)
		{
			throw new NullPointerException(PicoConstants::DATABASE_NONECTION_IS_NULL);
		}
		$this->executeDebug($sql);
		$stmt = $this->databaseConnection->prepare($sql);
		try 
		{
			$stmt->execute();
			return $stmt->rowCount() > 0;
		} 
		catch (PDOException $e) 
		{
			throw new PDOException($e);
		}
	}

	/**
	 * Fetch all result
	 *
	 * @param string $sql
	 * @param integer $tentativeType
	 * @param array $defaultValue
	 * @return array|null
	 */
	public function fetchAll($sql, $tentativeType = PDO::FETCH_ASSOC, $defaultValue = null)
	{
		if($this->databaseConnection == null)
		{
			throw new NullPointerException(PicoConstants::DATABASE_NONECTION_IS_NULL);
		}
		$result = array();
		$this->executeDebug($sql);
		$stmt = $this->databaseConnection->prepare($sql);
		try 
		{
			$stmt->execute();
			if ($stmt->rowCount() > 0) 
			{
				$result = $stmt->fetchAll($tentativeType);
			} 
			else 
			{
				$result = $defaultValue;
			}
		} 
		catch (PDOException $e) 
		{
			$result = $defaultValue;
		}
		return $result;
	}

	/**
	 * Execute query without return anything
	 * @param string $sql Query string to be executed
	 */
	public function execute($sql)
	{
		if($this->databaseConnection == null)
		{
			throw new NullPointerException(PicoConstants::DATABASE_NONECTION_IS_NULL);
		}
		$this->executeDebug($sql);
		$stmt = $this->databaseConnection->prepare($sql);
		try 
		{
			$stmt->execute();
		} 
		catch (PDOException $e) 
		{
			// Do nothing
		}
	}

	/**
	 * Execute query
	 * @param string $sql Query string to be executed
	 * @return PDOStatement|bool
	 */
	public function executeQuery($sql)
	{
		if($this->databaseConnection == null)
		{
			throw new NullPointerException(PicoConstants::DATABASE_NONECTION_IS_NULL);
		}
		$this->executeDebug($sql);
		$stmt = $this->databaseConnection->prepare($sql);
		try 
		{
			$stmt->execute();
		} 
		catch (PDOException $e) 
		{
			throw new PDOException($e);
		}
		return $stmt;
	}

	/**
	 * Execute query and sync to hub
	 * @param string $sql Query string to be executed
	 * @return PDOStatement|bool
	 */
	public function executeInsert($sql)
	{
		$stmt = $this->executeQuery($sql);
		$this->executeCallback($sql, self::QUERY_INSERT);
		return $stmt;
	}

	/**
	 * Execute update query
	 * @param string $sql Query string to be executed
	 * @return PDOStatement|bool
	 */
	public function executeUpdate($sql)
	{
		$stmt = $this->executeQuery($sql);
		$this->executeCallback($sql, self::QUERY_UPDATE);
		return $stmt;
	}

	/**
	 * Execute delete query
	 * @param string $sql Query string to be executed
	 * @return PDOStatement|bool
	 */
	public function executeDelete($sql)
	{
		$stmt = $this->executeQuery($sql);
		$this->executeCallback($sql, self::QUERY_DELETE);
		return $stmt;
	}

	/**
	 * Execute transaction query
	 * @param string $sql Query string to be executed
	 * @return PDOStatement|bool
	 */
	public function executeTransaction($sql)
	{
		$stmt = $this->executeQuery($sql);
		$this->executeCallback($sql, self::QUERY_TRANSACTION);
		return $stmt;
	}

	/**
	 * Execute callback query function
	 *
	 * @param string $query SQL to be executed
	 * @param string $type Query type
	 * @return void
	 */
	private function executeCallback($query, $type)
	{
		if($this->callbackExecuteQuery != null && is_callable($this->callbackExecuteQuery))
		{
			call_user_func($this->callbackExecuteQuery, $query, $type);
		}
	}
	
	/**
	 * Execute debug query function
	 *
	 * @param string $query SQL to be executed
	 * @return void
	 */
	private function executeDebug($query)
	{
		if($this->callbackDebugQuery != null && is_callable($this->callbackDebugQuery))
		{
			call_user_func($this->callbackDebugQuery, $query);
		}
	}

	/**
	 * Generate 20 bytes unique ID
	 * @return string 20 bytes
	 */
	public function generateNewId()
	{
		$uuid = uniqid();
		if ((strlen($uuid) % 2) == 1) {
			$uuid = '0' . $uuid;
		}
		$random = sprintf('%06x', mt_rand(0, 16777215));
		return sprintf('%s%s', $uuid, $random);
	}

	/**
	 * Get system variable
	 * @param string $variableName Variable name
	 * @param mixed $defaultValue Default value
	 * @return mixed System variable value of return default value if not exists
	 */
	public function getSystemVariable($variableName, $defaultValue = null)
	{
		$variableName = addslashes($variableName);
		$sql = "SELECT * FROM `pico_system_variable` 
		WHERE `system_variable_id` = '$variableName' ";
		$data = $this->executeQuery($sql)->fetch(PDO::FETCH_ASSOC);
		if (isset($data) && is_array($data) && !empty($data)) 
		{
			return $data['system_value'];
		} 
		else 
		{
			return $defaultValue;
		}
	}

	/**
	 * Set system variable
	 * @param string $variableName Variable name
	 * @param mixed $value Value to be set
	 */
	public function setSystemVariable($variableName, $value)
	{
		$currentTime = date('Y-m-d H:i:s');
		$variableName = addslashes($variableName);
		$value = addslashes($value);
		$sql = "SELECT * FROM `pico_system_variable` 
		WHERE `system_variable_id` = '$variableName' ";
		if ($this->executeQuery($sql)->rowCount() > 0) 
		{
			$sql = "UPDATE `pico_system_variable` 
			SET `system_value` = '$value', `time_edit` = '$currentTime' 
			WHERE `system_variable_id` = '$variableName' ";
			$this->executeUpdate($sql);
		} 
		else 
		{
			$sql = "INSERT INTO `pico_system_variable` 
			(`system_variable_id`, `system_value`, `time_create`, `time_edit`) VALUES
			('$variableName', '$value', '$currentTime' , '$currentTime')
			";
			$this->executeInsert($sql);
		}
	}

	/**
	 * Get the value of databaseCredentials
	 * @return PicoDatabaseCredentials
	 */
	public function getDatabaseCredentials()
	{
		return $this->databaseCredentials;
	}

	/**
	 * Get indication that database is connected or not
	 *
	 * @return bool
	 */ 
	public function isConnected()
	{
		return $this->connected;
	}

	/**
	 * Get database type
	 *
	 * @return string
	 */ 
	public function getDatabaseType()
	{
		return $this->databaseType;
	}
}