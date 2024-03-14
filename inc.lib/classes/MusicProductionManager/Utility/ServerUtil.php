<?php

namespace MusicProductionManager\Utility;

use MagicObject\MagicObject;

class ServerUtil
{
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
}