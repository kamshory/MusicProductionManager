<?php

namespace MusicProductionManager\Utility;

class StringUtil
{
    /**
     * Fixing cariage return
     *
     * @param string $text
     * @return string
     */
    public static function fixingCariageReturn($text)
    {
        $text = str_replace("\n", "\r\n", $text);
        $text = str_replace("\r\r\n", "\r\n", $text);
        $text = str_replace("\r", "\r\n", $text);
        $text = str_replace("\r\n\n", "\r\n", $text);
        $text = str_replace("\r\n\r\n", "\r\n", $text);
        $text = str_replace("\r\n\r\n", "\r\n", $text);
        return $text;
    }
}