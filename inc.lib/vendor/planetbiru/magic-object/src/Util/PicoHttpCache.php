<?php

namespace MagicObject\Util;

/**
 * Http cache
 * @link https://github.com/Planetbiru/MagicObject
 */
class PicoHttpCache
{
    /**
     * Send header to browser to cache current URL
     *
     * @param integer $lifetime Cache life time
     * @return void
     */
    public static function cacheLifetime($lifetime)
    {
        $ts = gmdate("D, d M Y H:i:s", time() + $lifetime) . " GMT";
        header("Expires: $ts");
        header("Pragma: cache");
        header("Cache-Control: max-age=$lifetime");
    }
}