<?php

namespace WS\Database;

use PDO;
use PDOException;
use PDOStatement;
use WS\Exceptions\NullPointerException;

class PicoDatabase // NOSONAR
{
	const DATABASE_CONNECTION_IS_NULL = "Database connection is null";

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

	private $lastQuery = "";

	/**
	 * Constructor
	 * @param PicoDatabaseCredentials $databaseCredentials
	 */
	public function __construct($databaseCredentials) // NOSONAR
	{
		$this->databaseCredentials = $databaseCredentials;
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
		try {
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
		} catch (PDOException $e) {
			// Do nothing
		}
		return $connected;
	}

	public function getDatabaseName()
	{
		return $this->databaseCredentials->getDatabaseName();
	}

	/**
	 * Get database connection
	 * @return PDO Represents a connection between PHP and a database server.
	 */
	public function getDatabaseConnection()
	{
		return $this->databaseConnection;
	}

	public function prepare($sql)
	{
		return $this->databaseConnection->prepare($sql);
	}

	/**
	 * Execute query
	 *
	 * @param string $sql
	 * @return integer
	 */
	public function exec($sql)
	{
		$count = func_num_args();
		if ($count > 1) {
			$params = array();
			for ($i = 0; $i < $count; $i++) {
				$params[] = func_get_arg($i);
			}
			$queryBuilder = new PicoDatabaseQueryBuilder($this);
			$buffer = $queryBuilder->createMatchedValue($params);
			$sql = $buffer;
		}

		return $this->databaseConnection->exec($sql);
	}
	/**
	 * Set time zone
	 *
	 * @param string $tz
	 * @return integer
	 */
	public function setTimeZone($tz)
	{
		return $this->exec("SET time_zone='$tz'");
	}
	public function query($sql)
	{
		return $this->databaseConnection->query($sql);
	}

	public function lastInsertId($name = null)
	{
		return $this->databaseConnection->lastInsertId($name);
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
		if ($this->databaseConnection == null) {
			throw new NullPointerException(self::DATABASE_CONNECTION_IS_NULL);
		}
		$result = array();
		$this->lastQuery = $sql;
		$stmt = $this->databaseConnection->prepare($sql);
		try {
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$result = $stmt->fetch($tentativeType);
			} else {
				$result = $defaultValue;
			}
		} catch (PDOException $e) {
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
		if ($this->databaseConnection == null) {
			throw new NullPointerException(self::DATABASE_CONNECTION_IS_NULL);
		}
		$this->lastQuery = $sql;
		$stmt = $this->databaseConnection->prepare($sql);
		try {
			$stmt->execute();
			return $stmt->rowCount() > 0;
		} catch (PDOException $e) {
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
		if ($this->databaseConnection == null) {
			throw new NullPointerException(self::DATABASE_CONNECTION_IS_NULL);
		}
		$result = array();
		$this->lastQuery = $sql;
		$stmt = $this->databaseConnection->prepare($sql);
		try {
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$result = $stmt->fetchAll($tentativeType);
			} else {
				$result = $defaultValue;
			}
		} catch (PDOException $e) {
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
		if ($this->databaseConnection == null) {
			throw new NullPointerException(self::DATABASE_CONNECTION_IS_NULL);
		}
		$this->lastQuery = $sql;
		$stmt = $this->databaseConnection->prepare($sql);
		try {
			$stmt->execute();
		} catch (PDOException $e) {
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
		if ($this->databaseConnection == null) {
			throw new NullPointerException(self::DATABASE_CONNECTION_IS_NULL);
		}
		$this->lastQuery = $sql;
		$stmt = $this->databaseConnection->prepare($sql);
		try {
			$stmt->execute();
		} catch (PDOException $e) {
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
		return $this->executeQuery($sql);
	}

	/**
	 * Execute update query
	 * @param string $sql Query string to be executed
	 * @return PDOStatement|bool
	 */
	public function executeUpdate($sql)
	{
		return $this->executeQuery($sql);
	}

	/**
	 * Execute delete query
	 * @param string $sql Query string to be executed
	 * @return PDOStatement|bool
	 */
	public function executeDelete($sql)
	{
		return $this->executeQuery($sql);
	}

	/**
	 * Execute transaction query
	 * @param string $sql Query string to be executed
	 * @return PDOStatement|bool
	 */
	public function executeTransaction($sql)
	{
		return $this->executeQuery($sql);
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
	 * Get the value of lastQuery
	 */
	public function getLastQuery()
	{
		return $this->lastQuery;
	}
}
