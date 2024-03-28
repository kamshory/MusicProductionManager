<?php

namespace WS\PicoWebSocket;

use WS\PicoWebSocket\Util\WebsocketClient;

class WSServer implements WSInterface
{
	protected $chatClients = array();
	protected $host = '127.0.0.1';
	protected $port = 8888;
	protected $masterSocket = null;
	protected $clientSockets = array();
	protected $dataChunk = 128;
	protected $maxHeaderSize = 2048;

	protected $sessionSavePath = '/';
	protected $sessionFilePrefix = 'sess_';
	protected $sessionCookieName = 'PHPSESSID';

	private $changed;
	
	public function __construct($host = '127.0.0.1', $port = 8888, $sessionSavePath = "/", $sessionFilePrefix = "sess_", $sessionCookieName = "PHPSESSID")
	{
		$this->host = $host;
		$this->port = $port;

		$this->masterSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		// stream_set_blocking($this->masterSocket, 0);
		// reuseable port
		socket_set_option($this->masterSocket, SOL_SOCKET, SO_REUSEADDR, 1);
		// bind socket to specified host
		socket_bind($this->masterSocket, 0, $this->port);
		// listen to port
		socket_listen($this->masterSocket);
		$this->clientSockets = array($this->masterSocket);
		$this->sessionSavePath = $sessionSavePath;
		$this->sessionFilePrefix = $sessionFilePrefix;
		$this->sessionCookieName = $sessionCookieName;
		
	}
	
	/**
	 * Check if web socket is running or not
	 *
	 * @return boolean
	 */
	public function isRunning()
	{
		$headers = array();
		$timeout = 1;
		$ssl = false;
		$persistant = false;
		$path = "/";
		$persistant = false;
		$context = null;
		$websocketAdmin = WebsocketClient::websocketOpen($this->host, $this->port, $headers, $error_string, $timeout, $ssl, $persistant, $path, $context);
		if($websocketAdmin !== false)
		{
			fclose($websocketAdmin);
		}
		return $websocketAdmin !== false;
	}
	
	/**
	 * Rinning server
	 *
	 * @return void
	 */
	public function run() // NOSONAR
	{
		$resourceId = 0;
		$null = null; //null var
		echo "Server started at port ".$this->port."\r\n";
		while (true) 
		{
			// manage multiple connections
			$this->changed = $this->clientSockets;
			// returns the socket resources in $this->changed array
			if(@socket_select($this->changed, $null, $null, 0, 10000) < 1)
			{
				continue;
			}
			// check for new socket
			if (in_array($this->masterSocket, $this->changed)) 
			{
				$clientSocket = socket_accept($this->masterSocket); //accpet new socket
				// stream_set_blocking($clientSocket, 0);
				$header = socket_read($clientSocket, $this->maxHeaderSize); //read data sent by the socket
				$header = trim($header, " \r\n ");
				if(strlen($header) > 2 && stripos($header, 'Sec-WebSocket-Key') !== false)
				{
					$resourceId++;
					socket_getpeername($clientSocket, $remoteAddress, $remotePort); //get ip address of connected socket
					$chatClient = new WSClient($resourceId, $clientSocket, $header, $remoteAddress, $remotePort, $this->sessionCookieName, $this->sessionSavePath, $this->sessionFilePrefix, $this, 'onClientLogin');
					$this->clientSockets[$resourceId] = $clientSocket; //add socket to client array
					$this->chatClients[$resourceId] = $chatClient;
					$this->onOpen($chatClient);
					$foundSocket = array_search($this->masterSocket, $this->changed);
					unset($this->changed[$foundSocket]);
				}
			}
			if(is_array($this->changed))
			{
				//loop through all connected sockets
				foreach ($this->changed as $resourceId => $changeSocket) 
				{
					//check for any incomming data
					
					$buffer = '';
					$buf1 = '';
					$nread = 0;
					do
					{
						$recv = @socket_recv($changeSocket, $buf1, $this->dataChunk, 0); 
						if($recv > 1)
						{
							$nread++;
							$buffer .= $buf1;
							if($recv < $this->dataChunk || $recv === false)
							{
								break;
							}
						}
						else
						{
							break;
						}
					}
					while($recv > 0);
						
					if($nread > 0 && strlen($buffer) > 0)
					{
						@socket_getpeername($changeSocket, $ip, $port); 
						$decodedData = WSUtility::unmask($buffer); 
						if(isset($decodedData['type']))
						{
							if($decodedData['type'] == 'close')
							{
								break;
							}
							else
							{
								$this->onMessage($this->chatClients[$resourceId], $decodedData['payload']);
								break;
							}
						}
						else
						{
							break;
						}
					}
					$buf2 = @socket_read($changeSocket, $this->dataChunk, PHP_NORMAL_READ);
					if ($buf2 === false) 
					{ 
						// check disconnected client
						// remove client for $clientSockets array
						$foundSocket = array_search($changeSocket, $this->clientSockets);
						if(isset($this->chatClients[$foundSocket]))
						{
							$closeClient = $this->chatClients[$foundSocket];
							unset($this->clientSockets[$foundSocket]);
							unset($this->chatClients[$foundSocket]);
							$this->onClose($closeClient);
						}
					}
				}
			}
		}
	}

	public function seal($data) 
	{
		return WSUtility::hybi10Encode($data);
	}
	
	public function unseal($data) 
	{
		$decodedData = WSUtility::hybi10Decode($data);
		return $decodedData['payload'];
	}

	public function onClientLogin($clientChat)
	{
		// do nothing
	}
	
	/**
	 * Method when a new client is connected
	 * @param WSClient $clientChat Chat client
	 * @param string $ip Remote adddress or IP address of the client 
	 * @param integer $port Remot port or port number of the client
	 */
	public function onOpen($clientChat)
	{
	}
	
	/**
	 * Method when a new client is disconnected
	 * @param WSClient $clientChat Chat client
	 * @param string $ip Remote adddress or IP address of the client 
	 * @param integer $port Remot port or port number of the client
	 */
	public function onClose($clientChat)
	{
	}
	
	/**
	 * Method when a client send the message
	 * @param WSClient $clientChat Chat client
	 * @param string $receivedText Text sent by the client
	 * @param string $ip Remote adddress or IP address of the client 
	 * @param integer $port Remot port or port number of the client
	 */
	public function onMessage($clientChat, $receivedText)
	{
	}
	
	/**
	 * Method to send the broadcast message to all client
	 * @param $message Message to sent to all client
	 */
	public function sendBroadcast($message)
	{
		foreach($this->chatClients as $client) 
		{
			$client->send($message);
		}
	}

	/**
	 * Destructor
	 */
	public function __destruct()
	{
		socket_close($this->masterSocket);
	}
}
