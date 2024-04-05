<?php
namespace MagicObject\Util;

use DOMDocument;
use DOMElement;

class TableUtil
{
    /**
     * Set class
     *
     * @param DOMElement $node
     * @param array $classList
     * @return DOMElement
     */
    public static function setClassList($node, $classList)
    {
        if(isset($classList) && is_array($classList))
        {
            $node->setAttribute("class", implode(" ", $classList));
        }
        return $node;
    }

    /**
     * Set attributes
     *
     * @param DOMElement $node
     * @param array $annotationClass
     * @return DOMElement
     */
    public static function setAttributes($node, $annotationAttributes)
    {
        if(isset($annotationAttributes) && is_array($annotationAttributes))
        {
            foreach($annotationAttributes as $attributeName=>$attributeValue)
            {
                $node->setAttribute($attributeName, $attributeValue);
            }            
        }
        return $node;
    }

    /**
     * Set identity
     *
     * @param DOMElement $node
     * @param ParameterObject $identity
     * @return DOMElement
     */
    public static function setIdentity($node, $identity)
    {
        if(isset($identity) && $identity->issetName())
        {
            $node->setAttribute("name", $identity->getName());      
        }
        return $node;
    }
    
    /**
     * Parse attribute
     *
     * @param string $attributes
     * @return array
     */
    public static function parseElementAttributes($attributes)
    {
        if(StringUtil::isNotNullAndNotEmpty($attributes))
        {
            $attributes = trim($attributes);
            if(StringUtil::startsWith($attributes, "("))
            {
                $attributes = trim(substr($attributes, 1));
            }
            if(StringUtil::endsWith($attributes, ")"))
            {
                $attributes = trim(substr($attributes, 0, strlen($attributes) - 1));
            }
            $doc = new DOMDocument();
            $doc->loadHTML("<html><body><div $attributes></div></body></html>");
            $div = $doc->getElementsByTagName("div")->item(0);
            for ($i = 0; $i < $div->attributes->length; ++$i) {
                $node = $div->attributes->item($i);
                $attrs[$node->nodeName] = $node->nodeValue;
            }
        }
        return $attrs;
    }
    
    /**
     * Validate class name of DOMElement
     *
     * @param string $className
     * @return boolean
     */
    public static function isValidClassName($className)
    {
        return !empty($className) && strpos($className, " ") === false && strpos($className, ".") === false && strpos($className, ",") === false;
    }
}