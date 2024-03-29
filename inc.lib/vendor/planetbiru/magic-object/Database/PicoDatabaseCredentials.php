<?php

namespace MagicObject\Database;

use MagicObject\SecretObject;
use MagicObject\Util\StringUtil;

class PicoDatabaseCredentials extends SecretObject
{
	/**
	 * Database driver
	 *
	 * @var string
	 */
	protected $driver = 'mysql';

	/**
	 * Database server host
	 *
	 * @EncryptIn
	 * @DecryptOut
	 * @var string
	 */
	protected $host = 'localhost';

	/**
	 * Database server port
	 * @var integer
	 */
	protected $port = 3306;

	/**
	 * Database username
	 *
	 * @EncryptIn
	 * @DecryptOut
	 * @var string
	 */
	protected $username = "";

	/**
	 * Database user password
	 *
	 * @EncryptIn
	 * @DecryptOut
	 * @var string
	 */
	protected $password = "";

	/**
	 * Database name
	 *
	 * @EncryptIn
	 * @DecryptOut
	 * @var string
	 */
	protected $databaseName = "";
	
	/**
	 * Database schema
	 *
	 * @EncryptIn
	 * @DecryptOut
	 * @var string
	 */
	protected $databseSchema = "public";

	/**
	 * Application time zone
	 *
	 * @var string
	 */
	protected $timeZone = "Asia/Jakarta";

	/**
	 * Magic method __get
	 *
	 * @param string $name Property name
	 * @return mixed
	 */
	public function __get($name)
	{
		$name = StringUtil::camelize($name);
		return $this->_get($name);
	}

	/**
	 * Magic method __set
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value)
	{
		$name = StringUtil::camelize($name);
		$this->_set($name, $value);
	}
}