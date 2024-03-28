<?php

namespace WS\PicoWebSocket;

class WSSessionConfig
{
    private $sessionCookieName = "";
    private $sessionSavePath = "";
    private $sessionSaveHandler = "";
    private $sessionFilePrefix = "";
    
    /**
     * Constructor
     *
     * @param string $sessionCookieName
     * @param string $sessionSaveHandler
     * @param string $sessionSavePath
     * @param string $sessionFilePrefix
     */
    public function __construct($sessionCookieName, $sessionSaveHandler, $sessionSavePath, $sessionFilePrefix)
    {
        $this->sessionCookieName = $sessionCookieName;
        $this->sessionSaveHandler = $sessionSaveHandler;
        $this->sessionSavePath = $sessionSavePath;
        $this->sessionFilePrefix = $sessionFilePrefix;
            
    }

    /**
     * Get the value of sessionCookieName
     */ 
    public function getSessionCookieName()
    {
        return $this->sessionCookieName;
    }

    /**
     * Set the value of sessionCookieName
     *
     * @return  self
     */ 
    public function setSessionCookieName($sessionCookieName)
    {
        $this->sessionCookieName = $sessionCookieName;

        return $this;
    }

    /**
     * Get the value of sessionSavePath
     */ 
    public function getSessionSavePath()
    {
        return $this->sessionSavePath;
    }

    /**
     * Set the value of sessionSavePath
     *
     * @return  self
     */ 
    public function setSessionSavePath($sessionSavePath)
    {
        $this->sessionSavePath = $sessionSavePath;

        return $this;
    }

    /**
     * Get the value of sessionSaveHandler
     */ 
    public function getSessionSaveHandler()
    {
        return $this->sessionSaveHandler;
    }

    /**
     * Set the value of sessionSaveHandler
     *
     * @return  self
     */ 
    public function setSessionSaveHandler($sessionSaveHandler)
    {
        $this->sessionSaveHandler = $sessionSaveHandler;

        return $this;
    }

    /**
     * Get the value of sessionFilePrefix
     */ 
    public function getSessionFilePrefix()
    {
        return $this->sessionFilePrefix;
    }

    /**
     * Set the value of sessionFilePrefix
     *
     * @return  self
     */ 
    public function setSessionFilePrefix($sessionFilePrefix)
    {
        $this->sessionFilePrefix = $sessionFilePrefix;

        return $this;
    }
}