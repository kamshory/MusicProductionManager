<?php

namespace MusicProductionManager\Utility;

class ServerUtil
{
    public static function getRemoteAddress()
    {
        return $_SERVER['REMOTE_ADDR'];
    }
}