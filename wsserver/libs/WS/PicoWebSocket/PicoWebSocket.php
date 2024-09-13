<?php

namespace WS\PicoWebSocket;

class PlanetWebSocket
{
	/**
	 * Server host
	*/
	private $host = '127.0.0.1';
	/**
	 * Server port
	 */
	private $port = 8888;	
	/**
	 * Socket
	 */
	private $socket = null;
	/**
	 * Client ports
	 */
	private $clients = array();
	/**
	 * Client object
	 */
	private $chatClients = array();
	private $maxDataSize = 65536;
	
	private $application = null;
	
	private $clientObject = null;
	
	/**
	* Constructor
	*/
	public function __construct($host, $port)
	{
		$this->clients = new \SplObjectStorage;
		$this->host = $host;
		$this->port = $port;
		$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		// reuseable port
		socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);
		// bind socket to specified host
		socket_bind($this->socket, 0, $this->port);
		// listen to port
		socket_listen($this->socket);
		$this->clients = array($this->socket);
	}
	
	/**
	 * Run websocket server
	 */
	public function run()
	{
		$index = 0;
		$null = null; //null var
		while (true) 
		{
			// manage multiple connections
			$changed = $this->clients;
			// returns the socket resources in $changed array
			@socket_select($changed, $null, $null, 0, 10);
			// check for new socket
			if (in_array($this->socket, $changed)) 
			{
				$socketNew = socket_accept($this->socket); //accpet new socket
				$index++;
				$header = socket_read($socketNew, 1024); //read data sent by the socket
				$this->performHandshaking($header, $socketNew, $this->host, $this->port); //perform websocket handshake
				socket_getpeername($socketNew, $ip, $port); //get ip address of connected socket
				$chatClient = new WSClient($index, $socketNew, $header, $ip, $port);
				if($chatClient->getSessions() != null)
				{
					if($chatClient->login())
					{
						$this->clients[$index] = $socketNew; //add socket to client array
						$this->chatClients[$index] = $chatClient;
						$this->onOpen($chatClient, $ip);
					}
				}
				
				//make room for new socket
				$foundSocket = array_search($this->socket, $changed);
				unset($changed[$foundSocket]);
			}
			if(is_array($changed))
			{
				//loop through all connected sockets
				foreach ($changed as $index => $changeSocket) 
				{
					//check for any incomming data
					while (@socket_recv($changeSocket, $buf, $this->maxDataSize, 0) >= 1) 
					{
						$receivedText = $this->unmask($buf); //unmask data
						socket_getpeername($changeSocket, $ip, $port); //get ip address of connected socket
						$this->onMessage($this->chatClients[$index], $receivedText, $ip, $port);
						break 2; //exist this loop
					}
					$buf = @socket_read($changeSocket, 1024, PHP_NORMAL_READ);
					if ($buf === false) 
					{ 
						// check disconnected client
						// remove client for $clients array
						$foundSocket = array_search($changeSocket, $this->clients);
						@socket_getpeername($changeSocket, $ip, $port);
						$closeClient = $this->chatClients[$foundSocket];
						unset($this->clients[$foundSocket]);
						unset($this->chatClients[$foundSocket]);
						$this->onClose($closeClient, $ip, $port);
					}
				}
			}
		}
	}
	
	/**
	 * Method when a new client is connected
	 * @param WSClient $clientChat Chat client
	 * @param string $ip Remote adddress or IP address of the client 
	 * @param integer $port Remot port or port number of the client
	 */
	public function onOpen($clientChat, $ip = '', $port = 0)
	{
		$response = json_encode(array('type' => 'system', 'message' => ' connected'));
		$this->sendBroadcast($response);
	}
	
	/**
	 * Method when a new client is disconnected
	 * @param WSClient $clientChat Chat client
	 * @param string $ip Remote adddress or IP address of the client 
	 * @param integer $port Remot port or port number of the client
	 */
	public function onClose($clientChat, $ip = '', $port = 0)
	{
		$response = json_encode(array('type' => 'system', 'message' => ' disconnected'));
		$this->sendBroadcast($response);
	}
	
	/**
	 * Method when a client send the message
	 * @param WSClient $clientChat Chat client
	 * @param string $receivedText Text sent by the client
	 * @param string $ip Remote adddress or IP address of the client 
	 * @param integer $port Remot port or port number of the client
	 */
	public function onMessage($clientChat, $receivedText, $ip = '', $port = 0)
	{
		$tst_msg = json_decode($receivedText, true); //json decode
		if(count($tst_msg))
		{
			$user_name = $tst_msg['name']; //sender name
			$user_message = htmlspecialchars($tst_msg['message'], ENT_QUOTES); //message text
			$response_text = json_encode(array('type' => 'usermsg', 'name' => $user_name, 'message' => $user_message));
			$this->sendBroadcast($response_text); //send data
		}
	}
	
	/**
	 * Method to send the broadcast message to all client
	 * @param $message Message to sent to all client
	 */
	public function sendBroadcast($message)
	{
		$maskedMessage = $this->mask($message);
		foreach ($this->clients as $changeSocket) 
		{
			@socket_write($changeSocket, $maskedMessage, strlen($maskedMessage));
		}
	}
	
	/**
	 * Method to send message to a client
	 * @param $changeSocket Client socket
	 * @param $message Message to sent to all client
	 * @return string Masked message
	 */
	public function sendMessage($changeSocket, $message)
	{
		$maskedMessage = $this->mask($message);
		@socket_write($changeSocket, $maskedMessage, strlen($maskedMessage));
	}
	
	/**
	 * Unmask incoming framed message
	 * @param $text Masked message
	 * @return string Plain text
	 */
	public function unmask($text)
	{
		$length = ord($text[1]) & 127;
		if ($length == 126) 
		{
			$masks = substr($text, 4, 4);
			$data = substr($text, 8);
		} 
		else if ($length == 127) 
		{
			$masks = substr($text, 10, 4);
			$data = substr($text, 14);
		} 
		else 
		{
			$masks = substr($text, 2, 4);
			$data = substr($text, 6);
		}
		$text = "";
		for ($i = 0; $i < strlen($data); ++$i) 
		{
			$text.= $data[$i] ^ $masks[$i % 4];
		}
		return $text;
	}
	
	/**
	 * Encode message for transfer to client
	 * @param $text Plain text to be sent to the client
	 * @return string Masked message
	 */
	public function mask($text)
	{
		$b1 = 0x80 | (0x1 & 0x0f);
		$length = strlen($text);
		if ($length <= 125) 
		{
			$header = pack('CC', $b1, $length);
		}
		else if ($length > 125 && $length < 65536)
		{ 
			$header = pack('CCn', $b1, 126, $length);
		}
		else if($length >= 65536)
		{
			$header = pack('CCNN', $b1, 127, $length);
		} 
		return $header . $text;
	}
	
	/**
	 * Handshake new client
	 * @param string $recevedHeader Request header sent by the client
	 * @param $client_conn Client connection
	 * @param string $host Host name of the websocket server
	 * @param integer $port Port number of the websocket server
	 */
	public function performHandshaking($recevedHeader, $client_conn, $host, $port)
	{
		$headers = array();
		$lines = preg_split("/\r\n/", $recevedHeader);
		foreach ($lines as $line) 
		{
			$line = chop($line);
			if (preg_match('/\A(\S+): (.*)\z/', $line, $matches)) 
			{
				$headers[$matches[1]] = $matches[2];
			}
		}
		if(isset($headers['Sec-WebSocket-Key']))
		{
			$secKey = $headers['Sec-WebSocket-Key'];
			$secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
			//hand shaking header
			$upgrade = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" 
				. "Upgrade: websocket\r\n" . "Connection: Upgrade\r\n" 
				. "WebSocket-Origin: $host\r\n" 
				. "WebSocket-Location: ws://$host:$port\r\n" 
				. "Sec-WebSocket-Accept: $secAccept\r\n"
				. "X-Engine: PlanetChat\r\n\r\n";
			socket_write($client_conn, $upgrade, strlen($upgrade));
		}
	}
	
	/**
	 * Convert UTF-8 to 8 bits HTML Entity code
	 * @param $string String to be converted
	 * @return string 8 bits HTML Entity code
	 */
	public function UTF8ToEntities($string)
	{
		if (!@preg_match("[\200-\237]", $string) && !@preg_match("[\241-\377]", $string))
			return $string;
		$string = preg_replace("/[\302-\375]([\001-\177])/","&#65533;\\1",$string);
		$string = preg_replace("/[\340-\375].([\001-\177])/","&#65533;\\1",$string);
		$string = preg_replace("/[\360-\375]..([\001-\177])/","&#65533;\\1",$string);
		$string = preg_replace("/[\370-\375]...([\001-\177])/","&#65533;\\1",$string);
		$string = preg_replace("/[\374-\375]....([\001-\177])/","&#65533;\\1",$string);
		$string = preg_replace("/[\300-\301]./", "&#65533;", $string);
		$string = preg_replace("/\364[\220-\277]../","&#65533;",$string);
		$string = preg_replace("/[\365-\367].../","&#65533;",$string);
		$string = preg_replace("/[\370-\373]..../","&#65533;",$string);
		$string = preg_replace("/[\374-\375]...../","&#65533;",$string);
		$string = preg_replace("/[\376-\377]/","&#65533;",$string);
		$string = preg_replace("/[\302-\364]{2,}/","&#65533;",$string);
		$string = preg_replace(
			"/([\360-\364])([\200-\277])([\200-\277])([\200-\277])/e",
			"'&#'.((ord('\\1')&7)<<18 | (ord('\\2')&63)<<12 |".
			" (ord('\\3')&63)<<6 | (ord('\\4')&63)).';'",
		$string);
		$string = preg_replace("/([\340-\357])([\200-\277])([\200-\277])/e",
		"'&#'.((ord('\\1')&15)<<12 | (ord('\\2')&63)<<6 | (ord('\\3')&63)).';'",
		$string);
		$string = preg_replace("/([\300-\337])([\200-\277])/e",
		"'&#'.((ord('\\1')&31)<<6 | (ord('\\2')&63)).';'",
		$string);
		$string = preg_replace("/[\200-\277]/","&#65533;",$string);
		return $string;
	}
	/**
	 * Destructor
	 */
	public function __destruct()
	{
		socket_close($this->socket);
	}
}
