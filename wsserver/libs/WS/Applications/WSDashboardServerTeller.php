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

class WSDashboardServerTeller extends WSServer implements WSInterface
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
	public function __construct($conf, $dbconf, $host = '127.0.0.1', $port = 8080)
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

		$iniWeb = parse_ini_file($this->conf->getIniWebPath());
		$cookieName = $iniWeb['app_cookie_name'];
		// Replace session cookie name
		$clientChat->setSessionCookieName($cookieName);
		$clientChat->updateSessionData();
		
		// Basic authentication is enabled		
		if($this->conf->isEnableAuthBasicTeller())
		{
			$loggedIn = $this->loginWithBasicAuth($clientChat);		
		}
		
		// Session authentication is enabled
		if(!$loggedIn && $this->conf->isEnableAuthSessionTeller())
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
	 * @return boolean
	 */
	private function loginWithBasicAuth($clientChat)
	{
		$basicAuth = new AuthorizationBasic($clientChat);
		$username = $basicAuth->getUsername();
		$password = $basicAuth->getPassword();
		$loggedIn = true;
		if($loggedIn)
		{
			$clientChat->setUsername($username);
		}
		$this->processWriteSession($clientChat, $username, $password);
		return $loggedIn;
	}
	
	/**
	 * Login with session
	 *
	 * @param WSClient $clientChat
	 * @return boolean
	 */
	private function loginWithSession($clientChat)
	{
		$sessionId = $this->getSessionId($clientChat);
		$sessions = WSUtility::getSessions($sessionId, $this->conf->getSessionSavePath(), $this->conf->getSessionFilePrefix());
		$keyUsername = $this->conf->getSessionAdminUsername();
		$keyPassword = $this->conf->getSessionAdminPassword();
		$username = isset($sessions[$keyUsername])?$sessions[$keyUsername]:"";
		$password = isset($sessions[$keyPassword])?$sessions[$keyPassword]:"";
		$loggedIn = $this->validUser($username, $password);
		if($loggedIn)
		{
			$clientChat->setUsername($username);
		}
		return $loggedIn;
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
	 * @return array
	 */
	public function onClientLogin($clientChat)
	{
		// Here are the client data
		// You can define it yourself
		return array(
			'login_time'=>date('Y-m-d H:i:s')
		);
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
		$this->routeMessage($clientChat, $message);
	}

	public function routeMessage($clientChat, $message)
	{
		$json = json_decode($message, true);
		$command = $json['command'];
		switch($command) {
			case 'broadcast-message':
			case 'bloadcast':
			case 'update-approval':
				$this->broadcastMessageAll($clientChat, $message);
				break;
			case 'send-message':
			case 'send':
					$recipients = $json['recipients'];
				$this->sendMessage($clientChat, $message, $recipients);
				break;
			default:
				$this->broadcastMessageAll($clientChat, $message);
			break;
		}

	}
	
	/**
	 * Broadcast message
	 * @param WSClient $clientChat Chat client
	 * @param string $message Text to sent
	 */
	public function broadcastMessageAll($clientChat, $message)
	{
		foreach($this->chatClients as $client)
		{
			if($client->getResourceId() != $clientChat->getResourceId())
			{
				$client->send($message);
			}
		}
	}
	
	/**
	 * Send message
	 * @param WSClient $clientChat Chat client
	 * @param string $message Text to sent
	 * @param string[] $receivers
	 */
	public function sendMessage($clientChat, $message, $recipients)
	{
		foreach($this->chatClients as $client)
		{
			if($client->getResourceId() != $clientChat->getResourceId() && in_array($clientChat->getUsername(), $recipients))
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
	 * @return boolean
	 */
	private function validUser($username, $password)
	{
		// Validate user here
		if(empty($username) || empty($password))
		{
			return false;
		}
		$result = false;
		try
		{
			$database = new PicoDatabase($this->dbconf);
			$database->connect();		
			$queryBuilder = new PicoDatabaseQueryBuilder($database);	
			$hashPassword = hash('sha512', $password);		
			$sqlCommand = $queryBuilder->newQuery()
                    ->select("teller.*")
                    ->from("teller")
                    ->where("teller.aktif = true
                        and teller.blokir = false
                        and teller.username like ?
                        and teller.pass_key = ? ", $username, $hashPassword)
                    ->toString();
			try
			{
				$result = $database->isRecordExists($sqlCommand);
			}
			catch(Exception $e)
			{
				$result = false;
			}
		}
		catch(Exception $e)
		{
			$result = false;
		}
		return $result;
	}

}
