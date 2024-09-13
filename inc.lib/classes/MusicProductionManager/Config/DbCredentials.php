<?php
namespace MusicProductionManager\Config;
use MagicObject\SecretObject;

/**
 * @JSON(property-naming-strategy=SNAKE_CASE)
 */
class DbCredentials extends SecretObject
{
    /**
	 * Database driver
	 *
	 * @DecryptOut
	 * @var string
	 */
	protected $driver;

	/**
	 * Database server host
	 *
	 * @DecryptOut
	 * @var string
	 */
	protected $host;

	/**
	 * Database server port
	 * @DecryptOut
	 * @var string
	 */
	protected $port;

	/**
	 * Database username
	 *
	 * @DecryptOut
	 * @var string
	 */
	protected $username;

	/**
	 * Database user password
	 *
	 * @DecryptOut
	 * @var string
	 */
	protected $password;
}