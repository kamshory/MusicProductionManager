<?php

namespace WS\Utils;

class PicoEnvironmentVariable
{
    /**
     * Replace all values with environment variable
     *
     * @param array $values
     * @return array
     */
    public function replaceSysEnvAll($values, $recursive = false)
    {
        foreach($values as $key=>$value)
        {
            if($recursive)
            {
                if(is_object($value) || is_array($value))
                {
                    $value = $this->replaceSysEnvAll($value, $recursive);
                }
                else
                {
                    $value = $this->replaceWithEnvironmentVariable($value);
                }
            }
            else
            {
                $value = $this->replaceWithEnvironmentVariable($value);
            }
            $values[$key] = $value;
        }
        return $values;
    }
    
    /**
     * Replace string with environment variable nane from a string
     *
     * @param string $value
     * @return string
     */
    public function replaceWithEnvironmentVariable($value)
    {
        $result = $value;
        $regex = '/\$\\{([^}]+)\\}/m';
        preg_match_all($regex, $value, $matches);
        $pair = array_combine($matches[0], $matches[1]);  

        if(!empty($pair))
        {
            foreach($pair as $key=>$value)
            {
                $systemEnv = getenv($value);
                if($systemEnv === false)
                {
                    // not found
                }
                else
                {
                    // found
                    $result = str_replace($key, $systemEnv, $result);
                }
            }
        }
        return $result;
    }
}