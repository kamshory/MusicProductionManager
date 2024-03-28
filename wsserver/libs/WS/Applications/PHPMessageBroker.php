<?php

namespace WS\Applications;

use Exception;
use MagicObject\Database\PicoDatabase;
use MagicObject\Database\PicoDatabaseQueryBuilder;
use WS\PicoWebSocket\AuthorizationBasic;
use WS\PicoWebSocket\WSClient;
use WS\PicoWebSocket\WSInterface;
use WS\PicoWebSocket\WSServer;
use WS\PicoWebSocket\WSUtility;

class PHPMessageBroker extends WSServer implements WSInterface
{
	/**
	 * Config
	 *
	 * @var Config
	 */
	private $conf;
	
	/**
	 * Database config
	 *
	 * @var PicoDatabaseCredentials
	 */
	private $dbconf;
	
	/**
	 * Constructor
	 *
	 * @param string $host
	 * @param integer $port
	 */
	public function __construct($host = '127.0.0.1', $port = 8080, $conf, $dbconf)
	{
		parent::__construct($host, $port, $conf->getSessionSavePath(), $conf->getSessionFilePrefix(), $conf->getSessionCookieName());
		$this->conf = $conf;
		$this->dbconf = $dbconf;
	}

	/**
	 * Method when a new client is connected
	 * @param WSClient $clientChat Chat client
	 */
	public function onOpen($clientChat)
	{
		$loggedIn = false;

		// Basic authentication is enabled		
		if($this->conf->isEnableAuthBasic())
		{
			$loggedIn = $this->loginWithBasicAuth($clientChat);		
		}
		
		// Session authentication is enabled
		if(!$loggedIn && $this->conf->isEnableAuthSession())
		{
			$loggedIn = $this->loginWithSession($clientChat);	
		}
		if(!$loggedIn)
		{
			$clientChat->close();
		}
	}

	/**
	 * Login with basic authentication
	 *
	 * @param WSClient $clientChat
	 * @return bool
	 */
	private function loginWithBasicAuth($clientChat)
	{
		$basicAuth = new AuthorizationBasic($clientChat);
		$username = $basicAuth->getUsername();
		$password = hash('sha256', $basicAuth->getPassword());
		$loggedIn = $this->validUser($username, $password);
		$clientChat->setUsername($username);
		return $loggedIn;
	}
	
	/**
	 * Login with session
	 *
	 * @param WSClient $clientChat
	 * @return bool
	 */
	private function loginWithSession($clientChat)
	{
		$sessionId = $this->getSessionId($clientChat);
		$sessions = WSUtility::getSessions($sessionId, $this->conf->getSessionSavePath(), $this->conf->getSessionFilePrefix());
		$keyUsername = $this->conf->getSessionUsername();
		$keyPassword = $this->conf->getSessionPassword();
		$username = isset($sessions[$keyUsername])?$sessions[$keyUsername]:"";
		$password = isset($sessions[$keyPassword])?$sessions[$keyPassword]:"";
		$clientChat->setUsername($username);
		return $this->validUser($username, $password);
	}
	
	/**
	 * Get session ID
	 *
	 * @param WSClient $clientChat
	 * @return string
	 */
	private function getSessionId($clientChat)
	{
		$cookies = $clientChat->getCookies();
		$cookieName = $clientChat->getSessionCookieName();
		$sessionId = $clientChat->getSessionId();
		return $sessionId;
	}
	
	/**
	 * Write username and password to session
	 *
	 * @param WSClient $clientChat
	 * @param string $username
	 * @param string $password
	 * @return void
	 */
	private function processWriteSession($clientChat, $username, $password)
	{
		$sessionId = $this->getSessionId($clientChat);
		$keyUsername = $this->conf->getSessionUsername();
		$keyPassword = $this->conf->getSessionUserpassword();
		$sessionData = array(
			$keyUsername=>$username,
			$keyPassword=>$password
		);	
		WSUtility::setSessions($sessionId, $sessionData, $this->conf->getSessionSavePath(), $this->conf->getSessionFilePrefix());	
	}
	
	/**
	 * Method when a new client is login
	 * @param WSClient $clientChat Chat client
	 */
	public function onClientLogin($clientChat)
	{
		// Here are the client data
		// You can define it yourself
		$clientData = array(
			'login_time'=>date('Y-m-d H:i:s')
		);
		return $clientData;
	}
	
	/**
	 * Method when a new client is disconnected
	 * @param WSClient $clientChat Chat client
	 */
	public function onClose($clientChat)
	{
		$clientData = $clientChat->getClientData();
	}
	
	/**
	 * Method when a client send the message
	 * @param WSClient $clientChat Chat client
	 * @param string $message Text sent by the client
	 */
	public function onMessage($clientChat, $message)
	{
		$obj = json_decode($message, true);
		if($obj['command'] == 'forward')
		{
			$recipient = $obj['recipient'];
			$this->forwardMessage($clientChat, $recipient, $message);
		}
		else if($obj['command'] == 'broardcast')
		{
			$this->broadcastMessage($clientChat, $message);
		}
	}

	public function forwardMessage($clientChat, $recipient, $message)
	{
		foreach($this->chatClients as $client)
		{
			if(trim($client->getUsername()) == trim($recipient))
			{
				$client->send($message);
			}
		}
	}
	
	/**
	 * Broadcast message
	 * @param WSClient $clientChat Chat client
	 * @param string $message Text to sent
	 */
	public function broadcastMessage($clientChat, $message)
	{
		foreach($this->chatClients as $client)
		{
			if($client->getResourceId() != $clientChat->getResourceId() 
			&& $client->getHostAndPath() == $clientChat->getHostAndPath())
			{
				$client->send($message);
			}
		}
	}
	
	/**
	 * Validate user
	 *
	 * @param string $username
	 * @param string $password
	 * @return bool
	 */
	private function validUser($username, $password)
	{
		// Validate user here
		if(empty($username) || empty($password))
		{
			return false;
		}
		try
		{
			$database = new PicoDatabase($this->dbconf);
			$database->connect();		
			$queryBuilder = new PicoDatabaseQueryBuilder($database);		
			$sql = $queryBuilder->newQuery()
				->select("*")
				->from("user")
				->where("user.username = ? and user.password = ? ", $username, $password);
			try
			{
				return $database->isRecordExists($sql);
			}
			catch(Exception $e)
			{
				return false;
			}
		}
		catch(Exception $e)
		{
			return false;
		}
	}

}
