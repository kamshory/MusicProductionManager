<?php

namespace MagicObject\Request;

class PicoRequest extends PicoRequestTool
{
    const ACTION_DETAIL = "detail";
    const ACTION_EDIT = "edit";
    const ACTION_ADD = "add";
    
    public function __construct($inputType = INPUT_GET)
    {
        parent::__construct();
        if($inputType == INPUT_GET && isset($_GET))
        {
            $this->loadData($_GET);
        }
        else if($inputType == INPUT_POST && isset($_POST))
        {
            $this->loadData($_POST);
        }
        else if($inputType == INPUT_COOKIE && isset($_COOKIE))
        {
            $this->loadData($_COOKIE);
        }
    }
    
    public static function getRequestBody()
    {
        return file_get_contents("php://input");
    }
    
    /**
     * Get all request headers
     *
     * @return array
     */
    public static function getRequestHeaders()
    {
        if (!function_exists('getallheaders')) {
            foreach ($_SERVER as $name => $value) {
                /* RFC2616 (HTTP/1.1) defines header fields as case-insensitive entities. */
                if (strtolower(substr($name, 0, 5)) == 'http_') {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
            return $headers;
        } else {
            return getallheaders();
        }
    }
    
    /**
     * Get request header
     *
     * @param string $key
     * @param array|null $allHeaders
     * @return string|null
     */
    public static function getRequestHeader($key, $allHeaders = null)
    {
        if($allHeaders == null)
        {
            $allHeaders = self::getRequestHeaders();
        }
        $key = strtolower($key);
        
        // fixing array
        $keys = array_keys($allHeaders);
        $values = array_values($allHeaders);
        foreach($keys as $k=>$key)
        {
            $keys[$k] = strtolower($key);
        }
        $allHeaders = array_combine($keys, $values);
        return isset($allHeaders[$key]) ? $allHeaders[$key] : null;      
    }
        
}