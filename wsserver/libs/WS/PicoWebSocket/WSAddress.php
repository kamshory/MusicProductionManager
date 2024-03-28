<?php

namespace WS\PicoWebSocket;

class WSAddress
{
    private $address = "";
    private $port = 0;
    
    /**
     * Constructor
     *
     * @param string $address
     * @param integer $port
     */
    public function __construct($address, $port)
    {
        $this->address = $address;
        $this->port = $port;
    }

    /**
     * Get the value of address
     */ 
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set the value of address
     *
     * @return  self
     */ 
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get the value of port
     */ 
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set the value of port
     *
     * @return  self
     */ 
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }
}