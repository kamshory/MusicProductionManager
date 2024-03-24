<?php

namespace MagicObject\Session;

class PicoSession
{

    const SESSION_STARTED = true;
    const SESSION_NOT_STARTED = false;
    const SAME_SITE_LAX = "Lax";
    const SAME_SITE_STRICT = "Strict";
    const SAME_SIZE_NONE = "None";

    // The state of the session
    private $sessionState = self::SESSION_NOT_STARTED;
    private $sessionStarted = false;

    // THE only instance of the class
    private static $instance;


    private function __construct()
    {
        // private constructor
    }

    /**
     * Returns THE instance of 'Session'.
     * The session is automatically initialized if it wasn't.
     * 
     * @param string $name
     * @return object
     **/
    public static function getInstance($name = null)
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
            self::$instance->setSessionName($name);
        }

        self::$instance->startSession();

        return self::$instance;
    }


    /**
     * (Re)starts the session.
     * 
     * @return bool true if the session has been initialized, else false.
     **/
    public function startSession()
    {
        if ($this->sessionState == self::SESSION_NOT_STARTED) {
            $this->sessionState = session_start();
        }
        $this->sessionStarted = true;
        return $this->sessionState;
    }

    /**
     * Check if session has been started or not
     *
     * @return boolean
     */
    public function isSessionStarted()
    {
        return $this->sessionStarted;
    }

    /**
     * Stores datas in the session.
     * Example: $instance->foo = 'bar';
     * 
     * @param $name Name of the datas.
     * @param $value Your datas.
     * @return void 
     **/
    public function __set($name, $value)
    {
        $_SESSION[$name] = $value;
    }


    /**
     * Gets datas from the session.
     * Example: echo $instance->foo;
     * 
     * @param $name Name of the datas to get.
     * @return mixed Datas stored in session.
     **/
    public function __get($name)
    {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }
    }

    public function __isset($name)
    {
        return isset($_SESSION[$name]);
    }

    public function __unset($name)
    {
        unset($_SESSION[$name]);
    }

    /**
     * Destroys the current session.
     * 
     * @return bool true is session has been deleted, else false.
     **/
    public function destroy()
    {
        if ($this->sessionState == self::SESSION_STARTED) {
            $this->sessionState = !session_destroy();
            unset($_SESSION);

            return !$this->sessionState;
        }
        return false;
    }

    /**
     * Set cookie params
     *
     * @param integer $maxlifetime
     * @param bool $secure
     * @param bool $httponly
     * @param string $samesite
     * @return self
     */
    public function setSessionCookieParams($maxlifetime, $secure, $httponly, $samesite = self::SAME_SIZE_NONE)
    {
        if (PHP_VERSION_ID < 70300) {
            session_set_cookie_params($maxlifetime, '/; samesite=' . $samesite, $_SERVER['HTTP_HOST'], $secure, $httponly);
        } else {
            session_set_cookie_params(array(
                'lifetime' => $maxlifetime,
                'path' => '/',
                'domain' => $_SERVER['HTTP_HOST'],
                'secure' => $secure,
                'httponly' => $httponly,
                'samesite' => $samesite
            ));
        }
        return $this;
    }

    /**
     * Support samesite cookie flag in both php 7.2 (current production) and php >= 7.3 (when we get there)
     * From: https://github.com/GoogleChromeLabs/samesite-examples/blob/master/php.md and https://stackoverflow.com/a/46971326/2308553 
     *
     * @see https://www.php.net/manual/en/function.setcookie.php
     *
     * @param string $name
     * @param string $value
     * @param int $expire
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httponly
     * @param string $samesite
     * @return self
     */
    function setSessionCookieSameSite($name, $value, $expire, $path, $domain, $secure, $httponly, $samesite = self::SAME_SIZE_NONE) //NOSONAR
    {
        if (PHP_VERSION_ID < 70300) {
            setcookie($name, $value, $expire, $path . '; samesite=' . $samesite, $domain, $secure, $httponly);
        }
        else {
            setcookie($name, $value, array(
                'expires' => $expire,
                'path' => $path,
                'domain' => $domain,
                'samesite' => $samesite,
                'secure' => $secure,
                'httponly' => $httponly,
            ));
        }
        return $this;
    }

    /**
     * Set session name
     *
     * @param string $ame
     * @return self
     */
    public function setSessionName($ame)
    {
        session_name($ame);
        return $this;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return string|false
     */
    public function setSessionSavePath($path)
    {
        return session_save_path($path);
    }

    /**
     * Set maximum lifetime
     *
     * @param integer $lifeTime
     * @return self
     */
    public function setSessionMaxLifeTime($lifeTime)
    {
        ini_set("session.gc_maxlifetime", $lifeTime);
        ini_set("session.cookie_lifetime", $lifeTime);
        return $this;
    }
    
    /**
     * Save session to redis
     *
     * @param string $host Redis host
     * @param integer $port Redis port
     * @param string $auth Redis auth
     * @return self
     */
    public function saveToRedis($host, $port, $auth)
    {
        $path = sprintf("tcp://%s:%d?auth=%s", $host, $port, $auth);
        ini_set("session.save_handler ", "redis");
        ini_set("session.save_path ", $path);
        return $this;
    }

    /**
     * Save session to files
     *
     * @param string $path Directory
     * @return self
     */
    public function saveToFiles($path)
    {
        ini_set("session.save_handler ", "files");
        ini_set("session.save_path ", $path);
        return $this;
    }
}
