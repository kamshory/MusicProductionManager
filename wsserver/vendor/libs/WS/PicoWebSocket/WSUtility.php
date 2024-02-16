<?php

namespace WS\PicoWebSocket;

use Exception;

class WSUtility
{
	/**
	 * Fix cariage return
	 *
	 * @param string $text
	 * @return string
	 */
	public static function fixCariageReturn($text)
	{
		$text = trim($text, "\r\n");
		$text = str_replace("\n", "\r\n", $text);
		$text = str_replace("\r\r\n", "\r\n", $text);
		$text = str_replace("\r", "\r\n", $text);
		$text = str_replace("\r\n\n", "\r\n", $text);
		return $text;
	}

	/**
	 * Parse request header
	 * @param string $header Request header from client
	 * @return array of the request header
	 */
	public static function parseHeaders($headers)
	{
		$headers = self::fixCariageReturn($headers);
		$arr = explode("\r\n", $headers);
		$arr2 = array();

		$firstLine = $arr[0];
		$arr4 = explode(" ", $firstLine);
		$method = @$arr4[0];
		$version = @$arr4[2];
		$path = '/';
		$requestURL = '/';
		$query = array();
		if (isset($arr4[1])) {
			$requestURL = $arr4[1];
			if (stripos($arr4[1], "?") !== false) {
				$arr5 = explode("?", $arr4[1], 2);
				$path = $arr5[0];
				@parse_str($arr5[1], $query);
			}
		}

		foreach ($arr as $idx => $value) {
			if ($idx > 0) {
				$arr3 = explode(": ", $value, 2);
				if (count($arr3) == 2) {
					$arr2[strtolower($arr3[0])] = $arr3[1];
				}
			}
		}
		
		return array(
			'method' => $arr4[0], 
			'uri' => $requestURL, 
			'path' => $path, 
			'query' => $query, 
			'version' => $version, 
			'headers' => $arr2
		);
	}
	
	/**
	 * Parse cookie
	 * @param $cookieString Cookie from client
	 * @return array of the cookie
	 */
	public static function parseCookie($cookieString)
	{
		$cookieData = array();
		$arr = explode("; ", $cookieString);
		foreach ($arr as $key => $val) {
			$arr2 = explode("=", $val, 2);
			if (count($arr2) > 1) {
				$cookieData[$arr2[0]] = $arr2[1];
			}
		}
		return $cookieData;
	}
	
	/**
	 * Read cookie
	 * @param $cookieData Associated array of the cookie
	 * @return string Cooke name
	 */
	public static function readCookie($cookieData, $name)
	{
		$v0 = (isset($cookieData[$name . "0"])) ? ($cookieData[$name . "0"]) : "";
		$v1 = (isset($cookieData[$name . "1"])) ? ($cookieData[$name . "1"]) : "";
		$v2 = (isset($cookieData[$name . "2"])) ? ($cookieData[$name . "2"]) : "";
		$v3 = (isset($cookieData[$name . "3"])) ? ($cookieData[$name . "3"]) : "";
		$v  = strrev(str_rot13($v1 . $v3 . $v2 . $v0));
		if ($v == "") {
			return md5(microtime() . mt_rand(1, 9999999));
		} else {
			return $v;
		}
	}
	
	/**
	 * Set session data
	 * @param string $sessionID Session ID
	 * @param string $sessionSavePath Session save path
	 * @param string $prefix Prefix of the session file name
	 * @return array contain session
	 */
	public static function getSessions($sessionID, $sessionSavePath = null, $prefix = "sess_")
	{
		$sessions = array();
		if ($sessionSavePath === null) {
			$sessionSavePath = session_save_path();
		}
		$path = $sessionSavePath . "/" . $prefix . $sessionID;
		if (file_exists($path)) {
			$sessionText = file_get_contents($path);
			if ($sessionText != '') {
				try
				{
					$sessions = WSSession::unserialize($sessionText);
				}
				catch(Exception $e)
				{
					// do nothing
				}
				return $sessions;
			}
		}
		return $sessions;
	}
	
	/**
	 * Undocumented function
	 *
	 * @param string  $sessionID
	 * @param array $sessionData
	 * @param string $sessionSavePath
	 * @param string $prefix
	 * @return void
	 */
	public static function setSessions($sessionID, $sessionData, $sessionSavePath = null, $prefix = "sess_")
	{
		$sessions = array();
		if ($sessionSavePath === null) {
			$sessionSavePath = session_save_path();
		}
		$path = $sessionSavePath . "/" . $prefix . $sessionID;
		
		// read file first
		if (file_exists($path)) {
			$sessionText = file_get_contents($path);
			if ($sessionText != '') {
				try
				{
					$sessions = WSSession::unserialize($sessionText);
				}
				catch(Exception $e)
				{
					// do nothing
				}
			}
		}
		
		// update sessions
		foreach($sessionData as $key=>$value)
		{
			$sessions[$key] = $value;
		}
		$encoded = WSSession::serialize($sessions);
		//file_put_contents($path, $encoded);
	}

	/**
	 * Unmask message
	 *
	 * @param string $data
	 * @return string
	 */
	public static function unmask($data)
	{
		return self::hybi10Decode($data);
	}

	/**
	 * Encodes a frame/message according the the WebSocket protocol standard.
     *
	 * @param string $payload
	 * @param string $type
	 * @param bool $masked
	 * @throws \RuntimeException
	 * @return string
	 */
	public static function hybi10Encode($payload, $type = 'text', $masked = true)
	{
		$frameHead = array();
		$payloadLength = strlen($payload);

		switch ($type) {
			case 'text':
				// first byte indicates FIN, Text-Frame (10000001):
				$frameHead[0] = 129;
				break;

			case 'close':
				// first byte indicates FIN, Close Frame(10001000):
				$frameHead[0] = 136;
				break;

			case 'ping':
				// first byte indicates FIN, Ping frame (10001001):
				$frameHead[0] = 137;
				break;

			case 'pong':
				// first byte indicates FIN, Pong frame (10001010):
				$frameHead[0] = 138;
				break;
		}

		// set mask and payload length (using 1, 3 or 9 bytes)
		if ($payloadLength > 65535) {
			$payloadLengthBin = str_split(sprintf('%064b', $payloadLength), 8);
			$frameHead[1] = ($masked === true) ? 255 : 127;
			for ($i = 0; $i < 8; $i++) {
				$frameHead[$i + 2] = bindec($payloadLengthBin[$i]);
			}
			// most significant bit MUST be 0 (close connection if frame too big)
			if ($frameHead[2] > 127) {
				throw new \RuntimeException('Invalid payload. Could not encode frame.');
			}
		} else if ($payloadLength > 125) {
			$payloadLengthBin = str_split(sprintf('%016b', $payloadLength), 8);
			$frameHead[1] = ($masked === true) ? 254 : 126;
			$frameHead[2] = bindec($payloadLengthBin[0]);
			$frameHead[3] = bindec($payloadLengthBin[1]);
		} else {
			$frameHead[1] = ($masked === true) ? $payloadLength + 128 : $payloadLength;
		}

		// convert frame-head to string:
		foreach (array_keys($frameHead) as $i) {
			$frameHead[$i] = chr($frameHead[$i]);
		}
		if ($masked === true) {
			// generate a random mask:
			$mask = array();
			for ($i = 0; $i < 4; $i++) {
				$mask[$i] = chr(rand(0, 255));
			}

			$frameHead = array_merge($frameHead, $mask);
		}
		$frame = implode('', $frameHead);

		// append payload to frame:
		for ($i = 0; $i < $payloadLength; $i++) {
			$frame .= ($masked === true) ? $payload[$i] ^ $mask[$i % 4] : $payload[$i];
		}

		return $frame;
	}


	/**
	 * Decodes a frame/message according to the WebSocket protocol standard.
	 *
	 * @param string $data
	 * @return array
	 */
	public static function hybi10Decode($data)
	{
		$unmaskedPayload = '';
		$decodedData = array();

		// estimate frame type:
		$firstByteBinary = sprintf('%08b', ord($data[0]));
		$secondByteBinary = sprintf('%08b', ord($data[1]));
		$opcode = bindec(substr($firstByteBinary, 4, 4));
		$isMasked = ($secondByteBinary[0] == '1') ? true : false;
		$payloadLength = ord($data[1]) & 127;

		// close connection if unmasked frame is received:
		if ($isMasked === false) {
		}

		switch ($opcode) {
				// text frame:
			case 1:
				$decodedData['type'] = 'text';
				break;
			case 2:
				$decodedData['type'] = 'binary';
				break;
				// connection close frame:
			case 8:
				$decodedData['type'] = 'close';
				break;
				// ping frame:
			case 9:
				$decodedData['type'] = 'ping';
				break;
				// pong frame:
			case 10:
				$decodedData['type'] = 'pong';
				break;
		}

		if ($payloadLength === 126) {
			$mask = substr($data, 4, 4);
			$payloadOffset = 8;
			$dataLength = bindec(sprintf('%08b', ord($data[2])) . sprintf('%08b', ord($data[3]))) + $payloadOffset;
		} elseif ($payloadLength === 127) {
			$mask = substr($data, 10, 4);
			$payloadOffset = 14;
			$tmp = '';
			for ($i = 0; $i < 8; $i++) {
				$tmp .= sprintf('%08b', ord($data[$i + 2]));
			}
			$dataLength = bindec($tmp) + $payloadOffset;
			unset($tmp);
		} else {
			$mask = substr($data, 2, 4);
			$payloadOffset = 6;
			$dataLength = $payloadLength + $payloadOffset;
		}

		/*
         * We have to check for large frames here. socket_recv cuts at 1024 bytes
         * so if websocket-frame is > 1024 bytes we have to wait until whole
         * data is transferd.
         */
		if (strlen($data) < $dataLength) {
			return array();
		}

		if ($isMasked === true) {
			for ($i = $payloadOffset; $i < $dataLength; $i++) {
				$j = $i - $payloadOffset;
				if (isset($data[$i])) {
					$unmaskedPayload .= $data[$i] ^ $mask[$j % 4];
				}
			}
			$decodedData['payload'] = $unmaskedPayload;
		} else {
			$payloadOffset = $payloadOffset - 4;
			$decodedData['payload'] = substr($data, $payloadOffset);
		}

		return $decodedData;
	}


	/**
	 * Encode message for transfer to client
	 * @param string $text Plain text to be sent to the client
	 * @return string Masked message
	 */
	public static function mask($text)
	{
		$b1 = 0x80 | (0x1 & 0x0f);
		$length = strlen($text);
		if ($length <= 125) {
			$header = pack('CC', $b1, $length);
		} else if ($length > 125 && $length < 65536) {
			$header = pack('CCn', $b1, 126, $length);
		} else if ($length >= 65536) {
			$header = pack('CCNN', $b1, 127, $length);
		}
		return $header . $text;
	}

	/**
	 * Convert UTF-8 to 8 bits HTML Entity code
	 * @param string String to be converted
	 * @return string 8 bits HTML Entity code
	 */
	public static function utf8ToEntities($string)
	{
		if (!@preg_match("[\200-\237]", $string) && !@preg_match("[\241-\377]", $string))
			return $string;
		$string = preg_replace("/[\302-\375]([\001-\177])/", "&#65533;\\1", $string);
		$string = preg_replace("/[\340-\375].([\001-\177])/", "&#65533;\\1", $string);
		$string = preg_replace("/[\360-\375]..([\001-\177])/", "&#65533;\\1", $string);
		$string = preg_replace("/[\370-\375]...([\001-\177])/", "&#65533;\\1", $string);
		$string = preg_replace("/[\374-\375]....([\001-\177])/", "&#65533;\\1", $string);
		$string = preg_replace("/[\300-\301]./", "&#65533;", $string);
		$string = preg_replace("/\364[\220-\277]../", "&#65533;", $string);
		$string = preg_replace("/[\365-\367].../", "&#65533;", $string);
		$string = preg_replace("/[\370-\373]..../", "&#65533;", $string);
		$string = preg_replace("/[\374-\375]...../", "&#65533;", $string);
		$string = preg_replace("/[\376-\377]/", "&#65533;", $string);
		$string = preg_replace("/[\302-\364]{2,}/", "&#65533;", $string);
		$string = preg_replace(
			"/([\360-\364])([\200-\277])([\200-\277])([\200-\277])/e",
			"'&#'.((ord('\\1')&7)<<18 | (ord('\\2')&63)<<12 |" .
				" (ord('\\3')&63)<<6 | (ord('\\4')&63)).';'",
			$string
		);
		$string = preg_replace(
			"/([\340-\357])([\200-\277])([\200-\277])/e",
			"'&#'.((ord('\\1')&15)<<12 | (ord('\\2')&63)<<6 | (ord('\\3')&63)).';'",
			$string
		);
		$string = preg_replace(
			"/([\300-\337])([\200-\277])/e",
			"'&#'.((ord('\\1')&31)<<6 | (ord('\\2')&63)).';'",
			$string
		);
		$string = preg_replace("/[\200-\277]/", "&#65533;", $string);
		return $string;
	}
}
