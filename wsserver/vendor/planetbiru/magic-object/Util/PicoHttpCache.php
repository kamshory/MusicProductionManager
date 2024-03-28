<?php

namespace MagicObject\Util;

class PicoHttpCache
{
    public static function cacheLifetime($lifetime)
    {
        $ts = gmdate("D, d M Y H:i:s", time() + $lifetime) . " GMT";
        header("Expires: $ts");
        header("Pragma: cache");
        header("Cache-Control: max-age=$lifetime");
    }
}