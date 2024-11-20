<?php

namespace MagicObject\Request;

use MagicObject\Util\ClassUtil\PicoObjectParser;

/**
 * Class for handling input from environment variables.
 * 
 * @author Kamshory
 * @package MagicObject\Request
 * @link https://github.com/Planetbiru/MagicObject
 */
class InputEnv extends PicoRequestBase {

    /**
     * Constructor for the InputEnv class.
     *
     * @param bool $recursive Flag to indicate if all objects should be converted recursively.
     * @param bool $parseNullAndBool Flag to indicate whether to parse NULL and BOOL values.
     * @param bool $forceScalar Flag to indicate if only scalar values should be retrieved.
     */
    public function __construct($recursive = false, $parseNullAndBool = false, $forceScalar = false)
    {
        parent::__construct($forceScalar);
        $this->_recursive = $recursive;

        if ($parseNullAndBool) {
            $this->loadData($this->forceBoolAndNull($_ENV));
        } else {
            $this->loadData($_ENV);
        }
    }

    /**
     * Override the loadData method to load environment variable data.
     *
     * @param array $data Data to load into the object.
     * @param bool $tolower Flag to indicate if the keys should be converted to lowercase (default is false).
     * @return self Returns the current instance for method chaining.
     */
    public function loadData($data, $tolower = false)
    {
        if ($this->_recursive) {
            $genericObject = PicoObjectParser::parseJsonRecursive($data);
            if ($genericObject !== null) {
                $values = $genericObject->valueArray();
                if ($values !== null && is_array($values)) {
                    $keys = array_keys($values);
                    foreach ($keys as $key) {
                        $this->{$key} = $genericObject->get($key);
                    }
                }
            }
        } else {
            parent::loadData($data);
        }
        return $this;
    }
}
