<?php

namespace Applications;

use WS\PicoWebSocket\WSInterface;
use WS\PicoWebSocket\WSServer;

class ChatServer extends WSServer implements WSInterface
{
	private $userOnSystem = array();
	/**
	 * Constructor
	 *
	 * @param string $host
	 * @param integer $port
	 */
	public function __construct($host = '127.0.0.1', $port = 8888)
	{
		parent::__construct($host, $port);
	}
	
	/**
	 * Update user on system
	 *
	 * @return void
	 */
	public function updateUserOnSystem()
	{
		$this->userOnSystem = array();
		foreach($this->chatClients as $client)
		{
			if(isset($client->getClientData()['username']))
			{
				$this->userOnSystem[$client->getClientData()['username']] = $client->getClientData();
			}
		}
	}
	
	/**
	 * Method when a new client is connected
	 * @param WSClient $clientChat Chat client
	 */
	public function onOpen($clientChat)
	{
		$clientData = $clientChat->getClientData();
		if(isset($clientData['username']))
		{
			$this->updateUserOnSystem();
			// Send user list
			$response = json_encode(
				array(
					'command' => 'user-on-system', 
					'data' => array(
						array(
							'users'=>$this->userOnSystem
						)
					)
				)
			);
			$this->sendBroadcast($response);

			// Send new user		
			$response = json_encode(
				array(
					'command' => 'user-login', 
					'data' => array(
						$clientChat->getSessions()
					)
				)
			);
			$this->sendBroadcast($response);
			$logInData = array(
				'command'=>'log-in',
				'data'=>array(
					array('my_id'=>$clientData['username'])
				)
			);
			$clientChat->send(json_encode($logInData));
		}
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
			'login_time'=>date('Y-m-d H:i:s'), 
			'username'=>$clientChat->getSessions()['planet_username'], 
			'full_name'=>$clientChat->getSessions()['planet_full_name'],
			'avatar'=>$clientChat->getSessions()['planet_avatar'],
			'sex'=>$clientChat->getSessions()['planet_sex']
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
		// Send user logout		
		if(isset($clientData['username']))
		{
			$this->updateUserOnSystem();
			// Send user list
			$response = json_encode(
				array(
					'command' => 'user-on-system', 
					'data' => array(
						array(
							'users'=>$this->userOnSystem
						)
					)
				)
			);
			$this->sendBroadcast($response);

			// Send new user		
			$response = json_encode(
				array(
					'command' => 'user-logout', 
					'data' => array(
						$clientChat->getSessions()
					)
				)
			);
			$this->sendBroadcast($response);
		}
	}
	
	/**
	 * Method when a client send the message
	 * @param WSClient $clientChat Chat client
	 * @param string $receivedText Text sent by the client
	 */
	public function onMessage($clientChat, $receivedText)
	{
		$json_message = json_decode($receivedText, true); 
		
		if(isset($json_message['command']))
		{
			$command = $json_message['command'];
			$unique_id = uniqid();
			$json_message['data'][0]['read'] = false;
			$json_message['data'][0]['unique_id'] = $unique_id;
			$json_message['data'][0]['timestamp'] = round(microtime(true)*1000);
			$json_message['data'][0]['date_time'] = date('j F Y H:i:s');
			$json_message['data'][0]['sender_name'] = $clientChat->getClientData()['full_name'];
			$json_message['data'][0]['sender_id'] = $clientChat->getClientData()['username'];
			
			if(isset($json_message['data'][0]['receiver_id']))
			{
				$receiver_id = $json_message['data'][0]['receiver_id'];
				$json_message['data'][0]['partner_id'] = $receiver_id;
				if(isset($this->userOnSystem[$receiver_id]))
				{
					if(isset($this->userOnSystem[$receiver_id]['full_name']))
					{
						$receiver_name = @$this->userOnSystem[$receiver_id]['full_name'];
						$json_message['data'][0]['receiver_name'] = $receiver_name;
					}
				}
			}
			
			if($command == 'send-message')
			{
				$this->processTextMessage($clientChat, $json_message);
			}
			else if($command == 'load-message')
			{
				$this->loadMessage($clientChat, $json_message); 
			}
			else if($command == 'mark-message')
			{
				$this->markMessage($clientChat, $json_message); 
			}
			else if($command == 'delete-message-for-all')
			{
				$this->deleteMessageForAll($clientChat, $json_message); 
			}
			else if($command == 'delete-message')
			{
				$this->deleteMessage($clientChat, $json_message); 
			}
			else if($command == 'clear-message')
			{
				$this->clearMessage($clientChat, $json_message); 
			}
			else if($command == 'video-call')
			{
				$this->videoCall($clientChat, $json_message); 
			}
			else if($command == 'on-call')
			{
				$this->onCall($clientChat, $json_message); 
			}
			else if($command == 'missed-call')
			{
				$this->missedCall($clientChat, $json_message); 
			}
			else if($command == 'reject-call')
			{
				$this->rejectCall($clientChat, $json_message); 
			}
			else if($command == 'client-call' || $command == 'client-accept' || $command == 'client-answer' || $command == 'client-offer' || $command == 'client-candidate')
			{
				$this->forwardWebRTCInfo($clientChat, $json_message); 
			}
			else if($command == 'receive-webrtc-info')
			{
				$this->forwardWebRTCInfo($clientChat, $json_message); 
			}
			else if($command == 'check-user-on-system')
			{
				$this->checkUserOnSystem($clientChat, $json_message);
			}
		}
			
	}

	/**
	 * Video call
	 *
	 * @param WSClient $clientChat Chat client
	 * @param array $json_message
	 * @return void
	 */
	public function videoCall($clientChat, $json_message)
	{
		$my_id = @$clientChat->getClientData()['username'];
		$sender_id = $clientChat->getClientData()['username'];
		$sender_name = $clientChat->getClientData()['full_name'];
		
		$receiver = $json_message['data'][0]['receiver_id'];
		$receiver_name = @$this->userOnSystem[$receiver]['full_name'];

		$json_message['data'][0]['partner_id'] = $sender_id;
		$json_message['data'][0]['partner_name'] = $clientChat->getClientData()['full_name'];
		$json_message['data'][0]['partner_uri'] = $clientChat->getClientData()['username'];
		$json_message['data'][0]['avatar'] = $clientChat->getClientData()['avatar'];
		
		foreach($this->chatClients as $client)
		{
			if($client->getClientData()['username'] == $receiver)
			{
				$client->send(json_encode($json_message));
			}
		}
	}
	/**
	 * Call
	 *
	 * @param WSClient $clientChat Chat client
	 * @param array $json_message
	 * @return void
	 */
	public function onCall($clientChat, $json_message)
	{
		$my_id = @$clientChat->getClientData()['username'];
		$sender_id = $clientChat->getClientData()['username'];
		$sender_name = $clientChat->getClientData()['full_name'];
		
		$receiver = $json_message['data'][0]['receiver_id'];
		$receiver_name = @$this->userOnSystem[$receiver]['full_name'];

		$json_message['data'][0]['partner_id'] = $sender_id;
		$json_message['data'][0]['partner_name'] = $clientChat->getClientData()['full_name'];
		$json_message['data'][0]['partner_uri'] = $clientChat->getClientData()['username'];
		$json_message['data'][0]['avatar'] = $clientChat->getClientData()['avatar'];
		
		foreach($this->chatClients as $client)
		{
			if($client->getClientData()['username'] == $receiver || $client->getClientData()['username'] == $sender_id)
			{
				$client->send(json_encode($json_message));
			}
		}
	}
	
	/**
	 * Missed call
	 *
	 * @param WSClient $clientChat Chat client
	 * @param array $json_message
	 * @return void
	 */
	public function missedCall($clientChat, $json_message)
	{
		$my_id = @$clientChat->getClientData()['username'];
		$sender_id = $clientChat->getClientData()['username'];
		$sender_name = $clientChat->getClientData()['full_name'];
		
		$receiver = $json_message['data'][0]['receiver_id'];
		$receiver_name = @$this->userOnSystem[$receiver]['full_name'];

		$json_message['data'][0]['partner_id'] = $sender_id;
		$json_message['data'][0]['partner_name'] = $clientChat->getClientData()['full_name'];
		$json_message['data'][0]['partner_uri'] = $clientChat->getClientData()['username'];
		$json_message['data'][0]['avatar'] = $clientChat->getClientData()['avatar'];
		
		foreach($this->chatClients as $client)
		{
			if($client->getClientData()['username'] == $receiver || $client->getClientData()['username'] == $sender_id)
			{
				$client->send(json_encode($json_message));
			}
		}
	}

	/**
	 * Reject call
	 *
	 * @param WSClient $clientChat Chat client
	 * @param array $json_message
	 * @return void
	 */
	public function rejectCall($clientChat, $json_message)
	{
		$my_id = @$clientChat->getClientData()['username'];
		$sender_id = $clientChat->getClientData()['username'];
		$sender_name = $clientChat->getClientData()['full_name'];
		
		$receiver = $json_message['data'][0]['receiver_id'];
		$receiver_name = @$this->userOnSystem[$receiver]['full_name'];

		$json_message['data'][0]['partner_id'] = $sender_id;
		$json_message['data'][0]['partner_name'] = $clientChat->getClientData()['full_name'];
		$json_message['data'][0]['partner_uri'] = $clientChat->getClientData()['username'];
		$json_message['data'][0]['avatar'] = $clientChat->getClientData()['avatar'];
		
		foreach($this->chatClients as $client)
		{
			if($client->getClientData()['username'] == $receiver || $client->getClientData()['username'] == $sender_id)
			{
				$client->send(json_encode($json_message));
			}
		}
	}

	/**
	 * Load message
	 *
	 * @param WSClient $clientChat Chat client
	 * @param array $json_message
	 * @return void
	 */
	public function loadMessage($clientChat, $json_message)
	{
		// TODO Add your code
	}

	/**
	 * Clear message
	 *
	 * @param WSClient $clientChat Chat client
	 * @param array $json_message
	 * @return void
	 */
	public function clearMessage($clientChat, $json_message)
	{
	}

	/**
	 * Delete message
	 *
	 * @param WSClient $clientChat Chat client
	 * @param array $json_message
	 * @return void
	 */
	public function deleteMessageForAll($clientChat, $json_message)
	{
		$my_id = @$clientChat->getClientData()['username'];
		$message_id_read = array();
		if($my_id)
		{
			if(isset($json_message['data']))
			{
				$data_all = $json_message['data'];
				foreach($data_all as $data)
				{
					if(isset($data['message_list']))
					{
						$partner_id = $data['receiver_id'];
						if(isset($this->userOnSystem[$partner_id]))
						{
							$partner_data = $this->userOnSystem[$partner_id];
							if(isset($data['message_list']))
							{
								$message_list = $data['message_list'];
								if(is_array($message_list))
								{
									if(count($message_list) > 0)
									{
										$reedback_message = array(
											'command'=>'delete-message-for-all',
											'data'=>array(
												array(
													'partner_id'=>$my_id,
													'flag'=>'read',
													'message_list'=>$message_list
												)
											)
										);
										foreach($this->chatClients as $client) 
										{
											$current_user_data = $client->getClientData();
											$member_id = $current_user_data['username'];
											if($partner_id == $member_id)
											{
												$reedback_message['data'][0]['partner_id'] = $my_id;
												$client->send(json_encode($reedback_message));
											}
											else if($member_id == $my_id) 
											{
												$reedback_message['data'][0]['partner_id'] = $partner_id;
												$client->send(json_encode($reedback_message));
											}
										}
									}
								}
							}
						}
						else
						{
							$partner_data = $this->userOnSystem[$partner_id];
						}
						if(isset($data['message_list']))
						{
							$message_list = $data['message_list'];
							if(is_array($message_list))
							{
								if(count($message_list) > 0)
								{
								}
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Mark message
	 *
	 * @param WSClient $clientChat Chat client
	 * @param array $json_message
	 * @return void
	 */
	public function markMessage($clientChat, $json_message)
	{
		$my_id = @$clientChat->getClientData()['username'];
		$message_id_read = array();
		if($my_id)
		{
			if(isset($json_message['data']))
			{
				$data_all = $json_message['data'];
				foreach($data_all as $data)
				{
					if(isset($data['message_list']))
					{
						$partner_id = $data['receiver_id'];
						if(isset($this->userOnSystem[$partner_id]))
						{
							$partner_data = $this->userOnSystem[$partner_id];
							if(isset($data['message_list']))
							{
								$message_list = $data['message_list'];
								if(is_array($message_list))
								{
									if(count($message_list) > 0)
									{
										$reedback_message = array(
											'command'=>'mark-message',
											'data'=>array(
												array(
													'partner_id'=>$my_id,
													'flag'=>'read',
													'message_list'=>$message_list
												)
											)
										);
										foreach($this->chatClients as $client) 
										{
											$current_user_data = $client->getClientData();
											$member_id = $current_user_data['username'];
											if($partner_id == $member_id)
											{
												$reedback_message['data'][0]['partner_id'] = $my_id;
												$client->send(json_encode($reedback_message));
											}
											else if($member_id == $my_id) 
											{
												$reedback_message['data'][0]['partner_id'] = $partner_id;
												$client->send(json_encode($reedback_message));
											}
										}
									}
								}
							}
						}
						else
						{
							$partner_data = $this->userOnSystem[$partner_id];
						}
						if(isset($data['message_list']))
						{
							$message_list = $data['message_list'];
							if(is_array($message_list))
							{
								if(count($message_list) > 0)
								{
								}
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Delete message
	 *
	 * @param WSClient $clientChat Chat client
	 * @param array $json_message
	 * @return void
	 */
	public function deleteMessage($clientChat, $json_message)
	{
		$my_id = @$clientChat->getClientData()['username'];
		$message_id_read = array();
		if($my_id)
		{
			if(isset($json_message['data']))
			{
				$data_all = $json_message['data'];
				foreach($data_all as $data)
				{
					if(isset($data['message_list']))
					{
						$partner_id = $data['receiver_id'];
						if(isset($data['message_list']))
						{
							$message_list = $data['message_list'];
							if(is_array($message_list))
							{
							}
						}
						
						if(isset($this->userOnSystem[$partner_id]))
						{
							$partner_data = $this->userOnSystem[$partner_id];
							if(isset($data['message_list']))
							{
								$message_list = $data['message_list'];
								if(is_array($message_list))
								{
									if(count($message_list) > 0)
									{
										$reedback_message = array(
											'command'=>'delete-message-for-all',
											'data'=>array(
												array(
													'partner_id'=>$partner_id,
													'flag'=>'read',
													'message_list'=>$message_list
												)
											)
										);
	
										foreach($this->chatClients as $client) 
										{
											$current_user_data = $client->getClientData();
											$member_id = $current_user_data['username'];
											if($member_id == $my_id) 
											{
												$client->send(json_encode($reedback_message));
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
	
	/**
	 * Check user
	 *
	 * @param WSClient $clientChat Chat client
	 * @param array $json_message
	 * @return void
	 */
	public function checkUserOnSystem($clientChat, $json_message)
	{
		$username = $json_message['data'][0]['username'];
		if(isset($this->userOnSystem[$username]))
		{
			$message = json_encode(
				array(
					'command'=>'check-user-on-system',
					'data'=>array(
						array(
							'username'=>$username,
							'available'=>false
							)
						)
					)
			);
		}
		else
		{
			$message = json_encode(
				array(
					'command'=>'check-user-on-system',
					'data'=>array(
						array(
							'username'=>$username,
							'available'=>true
							)
						)
					)
			);
		}
		$clientChat->send($message);
	}

	/**
	 * Process text message
	 *
	 * @param WSClient $clientChat Chat client
	 * @param array $json_message
	 * @return void
	 */
	public function processTextMessage($clientChat, $json_message)
	{
		$sender_id = $clientChat->getClientData()['username'];
		$sender_name = $clientChat->getClientData()['full_name'];
		
		$receiver = $json_message['data'][0]['receiver_id'];
		$receiver_name = @$this->userOnSystem[$receiver]['full_name'];


		$json_message['data'][0]['partner_id'] = $sender_id;
		$json_message['data'][0]['partner_name'] = $clientChat->getClientData()['full_name'];
		$json_message['data'][0]['partner_uri'] = $clientChat->getClientData()['username'];
		$json_message['data'][0]['avatar'] = $clientChat->getClientData()['avatar'];
		
		foreach($this->chatClients as $client)
		{
			if($client->getClientData()['username'] == $receiver)
			{
				$client->send(json_encode($json_message));
			}
		}
		$json_message['data'][0]['partner_id'] = $receiver;
		$json_message['data'][0]['partner_name'] = @$this->userOnSystem[$receiver]['full_name'];
		$json_message['data'][0]['partner_uri'] = @$this->userOnSystem[$receiver]['username'];
		$json_message['data'][0]['avatar'] = @$this->userOnSystem[$receiver]['avatar'];
		foreach($this->chatClients as $client)
		{

			if($client->getClientData()['username'] == $clientChat->getClientData()['username'])
			{
				$client->send(json_encode($json_message));
			}
		}
	}
	
	/**
	 * Forward WebRTC info
	 *
	 * @param WSClient $clientChat Chat client
	 * @param array $json_message
	 * @return void
	 */
	public function forwardWebRTCInfo($clientChat, $json_message)
	{
		$receiver = $json_message['data'][0]['receiver_id'];
		foreach($this->chatClients as $client)
		{
			if($client->getClientData()['username'] == $receiver)
			{
				$client->send(json_encode($json_message));
			}
		}
	}

}
