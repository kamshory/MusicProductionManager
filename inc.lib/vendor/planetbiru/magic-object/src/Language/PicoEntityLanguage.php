<?php

namespace MagicObject\Language;

use Exception;
use MagicObject\Database\PicoTableInfo;
use MagicObject\MagicObject;
use MagicObject\Util\ClassUtil\PicoAnnotationParser;
use MagicObject\Util\PicoGenericObject;
use MagicObject\Util\PicoStringUtil;
use MagicObject\Util\PicoTableUtil;
use ReflectionClass;
use stdClass;

class PicoEntityLanguage
{
    const ANNOTATION_TABLE = "Table";
    const ANNOTATION_LANGUAGE = "Language";

    private $_defaultColumnName = "key"; //NOSONAR
    
    /**
     * Current language
     *
     * @var string
     */
    private $_currentLanguage; //NOSONAR
    /**
     * Language
     *
     * @var PicoLanguage[]
     */
    private $_lableLanguage = array(); //NOSONAR
    
    /**
     * Table identity
     *
     * @var PicoGenericObject
     */
    private $_tableIdentity; //NOSONAR
        
    /**
     * Labels
     *
     * @var array
     */
    private $_labels = array(); //NOSONAR

    /**
     * Entity class name
     *
     * @var string
     */
    private string $_entityClassName = "";

    /**
     * Entity language
     *
     * @var string
     */
    private string $_entityLanguage = "";

    /**
     * Constructor
     *
     * @param MagicObject $data
     */
    public function __construct($entity = null)
    {
        if(isset($entity))
        {
            $this->loadEntityLabel($entity);
        }
    }
    
    /**
     * Load data to object
     * @param MagicObject $entity
     * @return self
     */
    public function loadEntityLabel($entity)
    {
        $this->_entityClassName = get_class($entity);
        $reflexClass = new PicoAnnotationParser($this->_entityClassName);
        $lang = $reflexClass->getFirstParameter(self::ANNOTATION_LANGUAGE);
        if($lang != null)
        {
            $prefLanguage = $reflexClass->parseKeyValueAsObject($lang);
            if($prefLanguage->issetContent())
            {
                $this->_entityLanguage = trim($prefLanguage->getContent());
                $this->_currentLanguage = $this->_entityLanguage;
            }  
        }
        else
        {
            $this->_entityLanguage = "__default";
            $this->_currentLanguage = $this->_entityLanguage;
        }
        $this->_tableIdentity = $reflexClass->parseKeyValueAsObject($reflexClass->getFirstParameter(self::ANNOTATION_TABLE));
        $propertyList = $this->propertyList(true);
        $defualtLanguage = array();
        foreach($propertyList as $prop)
        {
            $reflexProp = new PicoAnnotationParser($this->_entityClassName, $prop, PicoAnnotationParser::PROPERTY);            
            if($reflexProp != null)
            {
                $parameters = $reflexProp->getParametersAsObject();    
                if($parameters->issetLabel())
                {
                    $property = PicoStringUtil::camelize($prop);
                    $label = $this->label($reflexProp, $parameters, PicoStringUtil::camelToTitle($property));
                    $defualtLanguage[$property] = $label;
                }
            }
        }
        $this->addLanguage($this->_entityLanguage, $defualtLanguage, true);   
        return $this;
    }
    
    /**
     * Add language
     *
     * @param string $code
     * @param object|stdClass|array $reference
     * @param boolean $use
     * @return self
     */
    public function addLanguage($code, $reference, $use = false)
    {
        $this->_lableLanguage[$code] = new PicoLanguage($reference);
        if($use)
        {
            $this->selectLanguage($code);
        }
        return $this;
    }

    /**
     * Remove language
     *
     * @param string $code
     * @param stdClass|array $reference
     * @return self
     */
    public function removeLanguage($code)
    {
        if(isset($this->_lableLanguage[$code]))
        {
            unset($this->_lableLanguage[$code]);
        }
        if(!empty($this->_lableLanguage))
        {
            $keys = array_keys($this->_lableLanguage);
            $this->selectLanguage($keys[0]);
        }
        return $this;
    }
    
    /**
     * Set current language
     *
     * @param string $code
     * @return self
     */
    public function selectLanguage($code)
    {
        $this->_currentLanguage = $code;
        return $this;
    }
    
    /**
     * Property list
     * @var boolean $asArrayProps
     * @return array
     */
    protected function propertyList($asArrayProps = false)
    {
        $class = new ReflectionClass($this->_entityClassName);

        // filter only the calling class properties
        // skip parent properties
        $properties = array_filter(
            $class->getProperties(),
            function($property) use($class) {
                return $property->getDeclaringClass()->getName() == $class->getName();
            }
        );
        if($asArrayProps)
        {
            $result = array();
            $index = 0;
            foreach ($properties as $key) {
                $prop = $key->name;
                $result[$index] = $prop;
                
                $index++;
            }
            return $result;
        }
        else
        {
            return $properties;
        }
    }

    /**
     * Annotation content
     *
     * @param PicoAnnotationParser $reflexProp
     * @param PicoGenericObject $parameters
     * @param string $key
     * @param string $defaultLabel
     * @return mixed|null
     */
    private function annotationContent($reflexProp, $parameters, $annotation, $attribute)
    {
        if($parameters->get($annotation) != null)
        {
            $attrs = $reflexProp->parseKeyValueAsObject($parameters->get($annotation));
            if($attrs->get($attribute) != null)
            {
                return $attrs->get($attribute);
            }
        }
        return null;
    }
    
    /**
     * Define label
     *
     * @param PicoAnnotationParser $reflexProp
     * @param PicoGenericObject $parameters
     * @param string $defaultLabel
     * @return string
     */
    private function label($reflexProp, $parameters, $defaultLabel)
    {
        $lbl = $this->annotationContent($reflexProp, $parameters, "Label", "content");
        return PicoStringUtil::selectNotNull($lbl, $defaultLabel);
    }

    /**
     * Set property value
     *
     * @param string $propertyName
     * @param mixed|null
     * @return self
     */
    public function set($propertyName, $propertyValue)
    {
        $var = PicoStringUtil::camelize($propertyName);
        $this->$var = $propertyValue;
        return $this;
    }

    /**
     * Get property value
     *
     * @param string $propertyName
     * @return mixed|null
     */
    public function get($propertyName)
    {
        $var = PicoStringUtil::camelize($propertyName);
        if($this->_lableLanguage[$this->_currentLanguage] != null  && $this->_lableLanguage[$this->_currentLanguage]->get($var) != null)
        {
            return $this->_lableLanguage[$this->_currentLanguage]->get($var);
        }
        else 
        {
            return PicoStringUtil::camelToTitle($var);
        }
    }

    /**
     * Gets datas from the property.
     * Example: echo $instance->foo;
     * 
     * @param string $name Name of the property to get.
     * @return mixed Datas stored in property.
     **/
    public function __get($name)
    {
        if($this->__isset($name))
        {
            return $this->get($name);
        }
    }

    /**
     * Check if property has been set or not or has null value
     *
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->$name) ? $this->$name : null;
    }

    public function __call($method, $args) //NOSONAR
    {
        if(stripos($method, "get") === 0 && strlen($method) > 3)
        {
            $prop = lcfirst(substr($method, 3));
            return $this->get($prop);
        }
    }

    /**
     * Get table identity
     *
     * @return  PicoGenericObject
     */ 
    public function getTableIdentity()
    {
        return $this->_tableIdentity;
    }

    /**
     * Get entity language
     *
     * @return  string
     */ 
    public function getEntityLanguage()
    {
        return $this->_entityLanguage;
    }

    /**
     * Get entity class name
     *
     * @return  string
     */ 
    public function getEntityClassName()
    {
        return $this->_entityClassName;
    }
}