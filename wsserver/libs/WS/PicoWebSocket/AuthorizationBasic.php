<?php

namespace WS\PicoWebSocket;

class AuthorizationBasic
{

    private $username = '';
    private $password = '';

    /**
     * Constructor
     *
     * @param WSClient $wsClient
     */
    public function __construct($wsClient)
    {
        if (isset($wsClient->getHeaders()['authorization'])) {
            $auth = $wsClient->getHeaders()['authorization'];
            $this->parse($auth);
        }
    }

    public function parse($authorization)
    {
        if (!empty($authorization) && stripos($authorization, 'basic ') === 0) {
            $auth = substr($authorization, 6);
            $decoded = base64_decode($auth);
            $arr = explode(":", $decoded);
            if (count($arr) > 1) {
                $this->password = $arr[1];
            }
            $this->username = $arr[0];
        }
    }

    /**
     * Get the value of username
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Get the value of password
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
}
