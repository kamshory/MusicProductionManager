<?php

namespace WS\PicoWebSocket;

class WSSession
{
    /**
     * Unserialize session data
     *
     * @param string $sessionData
     * @param string $method
     * @return array
     */
    public static function unserialize($sessionData, $method = "php") {
        switch ($method) {
            case "php":
                return self::unserializePhp($sessionData);
                break;
            case "php_binary":
                return self::unserializePhpBinary($sessionData);
                break;
            default:
                throw new WSException("Unsupported session.serialize_handler: " . $method . ". Supported: php, php_binary");
        }
    }

    /**
     * Serialize session data
     *
     * @param array $array
     * @param string $method
     * @param bool $safe
     * @return string
     */
    public static function serialize($array, $method = "php", $safe = true) {
        // the session is passed as refernece, even if you dont want it to
        if( $safe ) 
        {
            $array = unserialize(serialize($array));
        }
        switch ($method) {
            case "php":
                return self::serializePhp($array);
                break;
            case "php_binary":
                return self::serializePhpBinary($array);
                break;
            default:
                throw new WSException("Unsupported session.serialize_handler: " . $method . ". Supported: php, php_binary");
        }
    }
    
    /**
     * Serialize session data with php method
     *
     * @param array $array
     * @return string
     */
    public static function serializePhp($array)
    {
        $raw = '' ;
        $line = 0 ;
        $keys = array_keys( $array ) ;
        foreach( $keys as $key ) {
            $value = $array[ $key ] ;
            $line ++ ;
            $raw .= $key .'|' ;
            if( is_array( $value ) && isset( $value['huge_recursion_blocker_we_hope'] )) {
                $raw .= 'R:'. $value['huge_recursion_blocker_we_hope'] . ';' ;
            } else {
                $raw .= serialize( $value ) ;
            }
            $array[$key] = Array( 'huge_recursion_blocker_we_hope' => $line ) ;
        }

        return $raw;
    }
    
    /**
     * Serialize session data with php_binaru method
     *
     * @param array $array
     * @return string
     */
    public static function serializePhpBinary($array)
    {
        $raw = "";
        foreach($array as $key=>$value)
        {
            $raw .= chr(strlen($key));
            $raw .= $key;
            $raw .= serialize($value);
        }
        return $raw;
    }


    /**
     * Unserialize session data with php method
     *
     * @param string $sessionData
     * @return array
     */
    private static function unserializePhp($sessionData) {
        $returnData = array();
        $offset = 0;
        while ($offset < strlen($sessionData)) {
            if (!strstr(substr($sessionData, $offset), "|")) {
                throw new WSException("invalid data, remaining: " . substr($sessionData, $offset));
            }
            $pos = strpos($sessionData, "|", $offset);
            $num = $pos - $offset;
            $varname = substr($sessionData, $offset, $num);
            $offset += $num + 1;
            $data = unserialize(substr($sessionData, $offset));
            $returnData[$varname] = $data;
            $offset += strlen(serialize($data));
        }
        return $returnData;
    }

    /**
     * Unserialize session data with php_binary method
     *
     * @param string $sessionData
     * @return array
     */
    private static function unserializePhpBinary($sessionData) {
        $returnData = array();
        $offset = 0;
        while ($offset < strlen($sessionData)) {
            $num = ord($sessionData[$offset]);
            $offset += 1;
            $varname = substr($sessionData, $offset, $num);
            $offset += $num;
            $data = unserialize(substr($sessionData, $offset));
            $returnData[$varname] = $data;
            $offset += strlen(serialize($data));
        }
        return $returnData;
    }
}