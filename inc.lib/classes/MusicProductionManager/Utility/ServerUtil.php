<?php

namespace MusicProductionManager\Utility;

use MagicObject\MagicObject;

class ServerUtil
{
    const OS_LINUX = 1;
    const OS_WINDOWS = 2;
    const OS_OTHER = 3;
    
    /**
     * Get remote address
     *
     * @param MagicObject $cfg
     * @return string
     */
    public static function getRemoteAddress($cfg = null)
    {
        if($cfg != null && $cfg->hasValueProxyProvider() && $cfg->getProxyProvider() == 'cloudflare')
        {
            // get remote address from header sent by Cloudflare
            return CloudflareUtil::getClientIp(false);
        }
        return $_SERVER['REMOTE_ADDR'];
    }
    
    /**
     * Is Windows
     *
     * @return boolean
     */
    public static function isWindows()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) == 'WIN';
    }
    
    /**
     * Get OS
     *
     * @return integer
     */
    public static function getOs()
    {
        $os = strtolower(php_uname('s'));
        if($os == "linux")
        {
            return self::OS_LINUX;
        }        
        else if($os == "windows nt")
        {
            return self::OS_WINDOWS;
        }
        else
        {
            return self::OS_OTHER;
        }
    }
}