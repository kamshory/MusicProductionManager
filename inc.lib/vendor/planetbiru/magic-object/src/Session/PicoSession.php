<?php

namespace MagicObject\Session;

use MagicObject\SecretObject;

/**
 * Class PicoSession
 * This class manages session handling, providing methods to configure and manipulate sessions.
 * 
 * The class provides an interface for session management, including handling session creation, destruction,
 * configuration, and the ability to store/retrieve session data.
 * 
 * @author Kamshory
 * @package MagicObject\Session
 * @link https://github.com/Planetbiru/MagicObject
 */
class PicoSession
{
    const SESSION_STARTED = true;
    const SESSION_NOT_STARTED = false;
    const SAME_SITE_LAX = "Lax";
    const SAME_SITE_STRICT = "Strict";
    const SAME_SITE_NONE = "None";

    /**
     * The state of the session.
     * 
     * The property name starts with an underscore to prevent child classes 
     * from overriding its value.
     *
     * @var boolean
     */
    private $_sessionState = self::SESSION_NOT_STARTED; // NOSONAR

    /**
     * The instance of the object.
     * 
     * The property name starts with an underscore to prevent child classes 
     * from overriding its value.
     *
     * @var self
     */
    private static $_instance; // NOSONAR


    /**
     * Constructor to initialize session configuration.
     *
     * This constructor accepts a session configuration object and applies settings such as
     * session name, max lifetime, and save handler (Redis or file system).
     *
     * @param SecretObject|null $sessConf Configuration for the session.
     */
    public function __construct($sessConf = null)
    {
        if ($sessConf && $sessConf->getName() != "") {
            $this->setSessionName($sessConf->getName());
        }
        if ($sessConf && $sessConf->getMaxLifeTime() > 0) {
            $this->setSessionMaxLifeTime($sessConf->getMaxLifeTime());
        }
        if ($sessConf && $sessConf->getSaveHandler() == "redis") {
            $path = $sessConf->getSaveHandler();
            $parsed = parse_url($path);
            parse_str($parsed['query'], $parsedStr);
            $this->saveToRedis($parsed['host'], $parsed['port'], $parsedStr['auth']);
        } elseif ($sessConf && $sessConf->getSaveHandler() == "files" && $sessConf->getSavePath() != "") {
            $this->saveToFiles($sessConf->getSavePath());
        }
    }

    /**
     * Returns the instance of PicoSession.
     * The session is automatically initialized if it wasn't already.
     *
     * This method ensures that only one instance of PicoSession is created (Singleton pattern).
     *
     * @param string|null $name Session name.
     * @param int $maxLifeTime Maximum lifetime of the session.
     * @return self The instance of PicoSession.
     */
    public static function getInstance($name = null, $maxLifeTime = 0)
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self;
            if (isset($name)) {
                self::$_instance->setSessionName($name);
            }
            if ($maxLifeTime > 0) {
                self::$_instance->setSessionMaxLifeTime($maxLifeTime);
            }
        }
        self::$_instance->startSession();
        return self::$_instance;
    }

    /**
     * (Re)starts the session.
     *
     * This method starts a session if it hasn't been started already.
     *
     * @return bool True if the session has been initialized, false otherwise.
     */
    public function startSession()
    {
        if ($this->_sessionState == self::SESSION_NOT_STARTED) {
            $this->_sessionState = session_start();
        }
        return $this->_sessionState;
    }

    /**
     * Checks if the session has been started.
     *
     * This method checks whether the current session has been started.
     *
     * @return bool True if the session has started, false otherwise.
     */
    public function isSessionStarted()
    {
        return $this->_sessionState;
    }

    /**
     * Stores data in the session.
     * Example: $_instance->foo = 'bar';
     *
     * This magic method stores data in the PHP session.
     *
     * @param string $name Name of the data.
     * @param mixed $value The data to store.
     * @return void
     */
    public function __set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * Retrieves data from the session.
     * Example: echo $_instance->foo;
     *
     * This magic method retrieves data from the PHP session.
     *
     * @param string $name Name of the data to retrieve.
     * @return mixed The data stored in session, or null if not set.
     */
    public function __get($name)
    {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
    }

    /**
     * Checks if a value is set in the session.
     *
     * This magic method checks whether a value is set in the session.
     *
     * @param string $name Name of the data to check.
     * @return bool True if the data is set, false otherwise.
     */
    public function __isset($name)
    {
        return isset($_SESSION[$name]);
    }

    /**
     * Unsets a value in the session.
     *
     * This magic method unsets data in the session.
     *
     * @param string $name Name of the data to unset.
     * @return void
     */
    public function __unset($name)
    {
        unset($_SESSION[$name]);
    }

    /**
     * Destroys the current session.
     *
     * This method destroys the session and clears all session data.
     *
     * @return bool true if the session has been deleted, else false.
     */
    public function destroy()
    {
        if ($this->_sessionState == self::SESSION_STARTED) {
            $this->_sessionState = !session_destroy();
            unset($_SESSION);
            return !$this->_sessionState;
        }
        return false;
    }

    /**
     * Sets cookie parameters for the session.
     *
     * This method sets parameters for the session cookie, including lifetime,
     * security attributes, and SameSite settings.
     *
     * @param int $maxlifetime Maximum lifetime of the session cookie.
     * @param bool $secure Indicates if the cookie should only be transmitted over a secure HTTPS connection.
     * @param bool $httponly Indicates if the cookie is accessible only through the HTTP protocol.
     * @param string $samesite The SameSite attribute of the cookie (Lax, Strict, None).
     * @return self Returns the current instance for method chaining.
     */
    public function setSessionCookieParams($maxlifetime, $secure, $httponly, $samesite = self::SAME_SITE_STRICT)
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
     * Sets a cookie with SameSite attribute support for different PHP versions.
     *
     * This method sets a cookie with the specified SameSite attribute, supporting both older and newer PHP versions.
     *
     * @param string $name The name of the cookie.
     * @param string $value The value of the cookie.
     * @param int $expire The expiration time of the cookie.
     * @param string $path The path on the server in which the cookie will be available.
     * @param string $domain The domain that the cookie is available to.
     * @param bool $secure Indicates if the cookie should only be transmitted over a secure HTTPS connection.
     * @param bool $httponly Indicates if the cookie is accessible only through the HTTP protocol.
     * @param string $samesite The SameSite attribute of the cookie (Lax, Strict, None).
     * @return self Returns the current instance for method chaining.
     */
    public function setSessionCookieSameSite($name, $value, $expire, $path, $domain, $secure, $httponly, $samesite = self::SAME_SITE_STRICT) // NOSONAR
    {
        if (PHP_VERSION_ID < 70300) {
            setcookie($name, $value, $expire, $path . '; samesite=' . $samesite, $domain, $secure, $httponly);
        } else {
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
     * Sets the session name.
     *
     * This method sets the session name for the current session.
     *
     * @param string $name The name of the session.
     * @return self Returns the current instance for method chaining.
     */
    public function setSessionName($name)
    {
        session_name($name);
        return $this;
    }

    /**
     * Sets the session save path.
     *
     * This method sets the path where session files will be stored.
     *
     * @param string $path The session save path.
     * @return string|false The session save path on success, false on failure.
     */
    public function setSessionSavePath($path)
    {
        return session_save_path($path);
    }

    /**
     * Sets the maximum lifetime for the session.
     *
     * This method sets the maximum lifetime of the session, affecting both garbage collection and cookie expiration.
     *
     * @param int $lifeTime Maximum lifetime for the session in seconds.
     * @return self Returns the current instance for method chaining.
     */
    public function setSessionMaxLifeTime($lifeTime)
    {
        ini_set("session.gc_maxlifetime", $lifeTime);
        ini_set("session.cookie_lifetime", $lifeTime);
        return $this;
    }

    /**
     * Saves the session to Redis.
     *
     * This method configures the session to be stored in Redis.
     *
     * @param string $host Redis host.
     * @param int $port Redis port.
     * @param string $auth Redis authentication.
     * @return self Returns the current instance for method chaining.
     */
    public function saveToRedis($host, $port, $auth)
    {
        $path = sprintf("tcp://%s:%d?auth=%s", $host, $port, $auth);
        ini_set("session.save_handler", "redis");
        ini_set("session.save_path", $path);
        return $this;
    }

    /**
     * Saves the session to files.
     *
     * This method configures the session to be stored in files.
     *
     * @param string $path The directory where session files will be stored.
     * @return self Returns the current instance for method chaining.
     */
    public function saveToFiles($path)
    {
        ini_set("session.save_handler", "files");
        ini_set("session.save_path", $path);
        return $this;
    }

    /**
     * Retrieves the current session ID.
     *
     * This method retrieves the current session ID, if any.
     *
     * @return string The current session ID.
     */
    public function getSessionId()
    {
        return @session_id();
    }

    /**
     * Sets a new session ID.
     *
     * This method sets a new session ID for the current session.
     *
     * @param string $id The new session ID.
     * @return self Returns the current instance for method chaining.
     */
    public function setSessionId($id)
    {
        @session_id($id);
        return $this;
    }
}
