<?php

namespace MagicObject\Database;

use PDO;
use PDOException;
use PDOStatement;
use MagicObject\Constants\PicoConstants;
use MagicObject\Exceptions\NullPointerException;
use stdClass;

/**
 * Database connection for MagicObject
 * Developer: Kamshory
 * @link https://github.com/Planetbiru/MagicObject
 */
class PicoDatabase //NOSONAR
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
	 * Autocommit
	 *
	 * @var boolean
	 */
	private $autocommit = true;

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
	 * 
	 * @param PicoDatabaseCredentials $databaseCredentials
	 * @param callable $callbackExecuteQuery Callback when execute query that modify data. Parameter 1 is SQL, parameter 2 is one of query type (PicoDatabase::QUERY_INSERT, PicoDatabase::QUERY_UPDATE, PicoDatabase::QUERY_DELETE, PicoDatabase::QUERY_TRANSACTION)
	 * @param callable $callbackDebugQuery Callback when execute all queries. Parameter 1 is SQL
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
	 * @return boolean true if success and false if failed
	 */
	public function connect()
	{
		date_default_timezone_set($this->databaseCredentials->getTimeZone());
		$timeZoneOffset = date("P");
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
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '$timeZoneOffset';",
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
	 * Disconnect from database
	 *
	 * @return void
	 */
	public function disconnect()
	{
		$this->databaseConnection = null;
	}
	
	/**
	 * Set time zone offset
	 * Time zone offset is difference to Greenwich time (GMT) with colon between hours and minutes. See date("P") on PHP manual
	 *
	 * @param string $timeZoneOffset
	 * @return self
	 */
	public function setTimeZoneOffset($timeZoneOffset)
	{
		$sql = "SET time_zone = '$timeZoneOffset';";
		$this->execute($sql);
		return $this;
	}
	
	/**
	 * Set autocommit ON of OFF
	 * When it set to OFF, user MUST call commit or rollback manualy. Default action is rollback
	 *
	 * @param boolean $autocommit
	 * @return boolean
	 */
	public function setAudoCommit($autocommit)
	{
		$this->autocommit = $autocommit;
		return $this->databaseConnection->setAttribute(PDO::ATTR_AUTOCOMMIT, $autocommit ? 1 : 0);
	}
	
	/**
	 * Commit
	 *
	 * @return boolean
	 */
	public function commit()
	{
		return $this->databaseConnection->commit();
	}
	
	/**
	 * Rollback
	 *
	 * @return boolean
	 */
	public function rollback()
	{
		return $this->databaseConnection->rollback();
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
	 * Execute query
	 *
	 * @param string $sql
	 * @return PDOStatement|false
	 * @throws PDOException
	 */
	public function query($sql)
	{
		return $this->executeQuery($sql);
	}

	/**
	 * Fetch result
	 *
	 * @param string $sql
	 * @param integer $tentativeType
	 * @param array $defaultValue
	 * @return array|object|stdClass|null
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
	 * @return boolean
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
	 * @return PDOStatement|boolean
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
	 * @return PDOStatement|boolean
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
	 * @return PDOStatement|boolean
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
	 * @return PDOStatement|boolean
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
	 * @return PDOStatement|boolean
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
	 * Get last insert ID
	 *
	 * @param string $name
	 * @return string|false
	 */
	public function lastInsertId($name = null)
	{
		return $this->databaseConnection->lastInsertId($name);
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
	 * @return boolean
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
	
	/**
	 * Convert object to string for debug
	 *
	 * @return string
	 */
	public function __toString()
	{
		$val = new stdClass;
		$val->databaseType = $this->databaseType;
		$val->autocommit = $this->autocommit;
		$val->connected = $this->connected;
		return json_encode($val);
	}
}