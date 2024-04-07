<?php

namespace MagicObject;

use DOMDocument;
use MagicObject\Util\PicoGenericObject;
use MagicObject\Util\PicoAnnotationParser;
use MagicObject\Util\StringUtil;
use MagicObject\Util\TableUtil;
use ReflectionClass;
use stdClass;

class DataTable extends SetterGetter
{
    const ANNOTATION_TABLE = "Table";
    const ANNOTATION_ATTRIBUTES = "Attributes";
    const CLASS_LIST = "ClassList";
    const ANNOTATION_ID = "Id";
    const ANNOTATION_COLUMN = "Column";
    const ANNOTATION_VAR = "var";
    const ANNOTATION_GENERATED_VALUE = "GeneratedValue";
    const ANNOTATION_NOT_NULL = "NotNull";
    const ANNOTATION_DEFAULT_COLUMN = "DefaultColumn";
    const ANNOTATION_DEFAULT_COLUMN_LABEL = "DefaultColumnLabel";
    const ANNOTATION_LANGUAGE = "Language";
    const KEY_PROPERTY_TYPE = "property_type";
    const KEY_PROPERTY_NAME = "property_name";
    
    const KEY_NAME = "name";
    const KEY_CLASS = "class";
    const KEY_VALUE = "value";
    const SQL_DATE_TIME_FORMAT = "Y-m-d H:i:s";
    const DATE_TIME_FORMAT = "datetimeformat";
    
    const TAG_TABLE = "table";
    const TAG_THEAD = "thead";
    const TAG_TBODY = "tbody";
    const TAG_TR = "tr";
    const TAG_TH = "th";
    const TAG_TD = "td";
    
    const TD_LABEL = "td-label";
    const TD_VALUE = "td-value";
    
    private $attributes = array();
    private $classList = array();
    private $defaultColumnName = "key";
    
    /**
     * Current language
     *
     * @var string
     */
    private $currentLanguage;
    /**
     * Language
     *
     * @var PicoLanguage[]
     */
    private $lableLanguage = array();
    
    /**
     * Table identity
     *
     * @var PicoGenericObject
     */
    private $tableIdentity;
      
    /**
     * Constructor
     *
     * @param MagicObject|self|stdClass|array $data
     */
    public function __construct($data = null)
    {
        if(isset($data))
        {
            $this->loadData($data);
        }
        $this->init();
    }
    
    /**
     * Load data to object
     * @param mixed $data
     * @return self
     */
    public function loadData($data)
    {
        if($data != null)
        {
            if($data instanceof MagicObject)
            {
                $values = $data->value();
                foreach ($values as $key => $value) {
                    $key2 = StringUtil::camelize($key);
                    $this->set($key2, $value);
                }
            }
            else if (is_array($data) || is_object($data)) {
                foreach ($data as $key => $value) {
                    $key2 = StringUtil::camelize($key);
                    $this->set($key2, $value);
                }
            }
        }
        return $this;
    }
    /**
     * Add language
     *
     * @param string $code
     * @param stdClass|array $reference
     * @return self
     */
    public function addLanguage($code, $reference)
    {
        $this->lableLanguage[$code] = new PicoLanguage($reference);
        return $this;
    }
    
    /**
     * Add language
     *
     * @param string $code
     * @return self
     */
    public function selectLanguage($code)
    {
        $this->currentLanguage = $code;
        return $this;
    }
    
    private function init()
    {
        $className = get_class($this);
        $reflexClass = new PicoAnnotationParser($className);
        $this->attributes = TableUtil::parseElementAttributes($reflexClass->getFirstParameter(self::ANNOTATION_ATTRIBUTES));    
        $classList = $reflexClass->parseKeyValueAsObject($reflexClass->getFirstParameter(self::CLASS_LIST));
        $prefLanguage = $reflexClass->parseKeyValueAsObject($reflexClass->getFirstParameter(self::ANNOTATION_LANGUAGE));
        $defaultColumnName = $reflexClass->parseKeyValueAsObject($reflexClass->getFirstParameter(self::ANNOTATION_DEFAULT_COLUMN_LABEL));
        if($defaultColumnName->issetContent())
        {
            $this->defaultColumnName = $defaultColumnName->getContent();
        }    
        if($classList->issetContent())
        {
            $this->classList = explode(" ", preg_replace('/\s+/', ' ', $classList->getContent()));
            $this->classList = array_unique($this->classList);
        }
        if($prefLanguage->issetContent())
        {
            $this->currentLanguage = $prefLanguage->getContent();
        }  
        $this->tableIdentity = $reflexClass->parseKeyValueAsObject($reflexClass->getFirstParameter(self::ANNOTATION_TABLE));
    }
    
    /**
     * Add class to table
     *
     * @param string $className
     * @return self
     */
    public function addClass($className)
    {
        if(TableUtil::isValidClassName($className))
        {
            $this->classList[] = $className;
            $this->classList = array_unique($this->classList);
        }
        return $this;
    }
    
    /**
     * Remove class from table
     *
     * @param string $className
     * @return self
     */
    public function removeClass($className)
    {
        if(TableUtil::isValidClassName($className))
        {
            $tmp = array();
            foreach($this->classList as $cls)
            {
                if($cls != $className)
                {
                    $tmp[] = $cls;
                }
            }
            $this->classList = $tmp;
        }
        return $this;
    }
    
    /**
     * Replace class of the table
     *
     * @param string $search
     * @param string $replace
     * @return self
     */
    public function replaceClass($search, $replace)
    {
        $this->removeClass($search);
        $this->addClass($replace);
        return $this;
    }
    
    /**
     * Property list
     * @var bool $reflectSelf
     * @var bool $asArrayProps
     * @return array
     */
    protected function propertyList($reflectSelf = false, $asArrayProps = false)
    {
        $reflectionClass = $reflectSelf ? self::class : get_called_class();
        $class = new ReflectionClass($reflectionClass);

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
     * Define label
     *
     * @param PicoAnnotationParser $reflexProp
     * @param PicoGenericObject $parameters
     * @param string $key
     * @param string $defaultLabel
     * @return string
     */
    private function label($reflexProp, $parameters, $key, $defaultLabel)
    {
        $label = $defaultLabel;
        if(stripos($this->defaultColumnName, "->"))
        {
            $cn = explode("->", $this->defaultColumnName);
            $lbl = $this->annotationContent($reflexProp, $parameters, trim($cn[0]), trim($cn[1]));
            $label = StringUtil::selectNotNull($lbl, $defaultLabel);
            
        }
        else if($this->defaultColumnName == self::ANNOTATION_LANGUAGE)
        {
            if(isset($this->lableLanguage) && isset($this->lableLanguage[$this->currentLanguage]))
            {
                $label = $this->lableLanguage[$this->currentLanguage]->isset($key) ? $this->lableLanguage[$this->currentLanguage]->get($key) : $defaultLabel;
            }
            else
            {
                $lbl = $this->annotationContent($reflexProp, $parameters, "Label", "content");
                $label = StringUtil::selectNotNull($lbl, $defaultLabel);
            }
            
        }
        return $label;
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
     * Magic method to string
     *
     * @return string
     */
    public function __toString()
    {
        $className = get_class($this);
        $doc = new DOMDocument();
        $table = $doc->appendChild($doc->createElement(self::TAG_TABLE));

        TableUtil::setAttributes($table, $this->attributes);
        TableUtil::setClassList($table, $this->classList);
        TableUtil::setIdentity($table, $this->tableIdentity);
       
        $tbody = $table->appendChild($doc->createElement(self::TAG_TBODY));
        $doc->formatOutput = true;
        
        $props = $this->propertyList();
        
        foreach($props as $prop)
        {
            $key = $prop->name;
            $label = $key;
            $value = $this->get($key);
            $tr = $tbody->appendChild($doc->createElement(self::TAG_TR));
            
            $reflexProp = new PicoAnnotationParser($className, $key, PicoAnnotationParser::PROPERTY);
            
            if($reflexProp != null)
            {
                $parameters = $reflexProp->getParametersAsObject();
                if($parameters->issetLabel())
                {
                    $label = $this->label($reflexProp, $parameters, $key, $label);
                }
            }

            $td1 = $tr->appendChild($doc->createElement(self::TAG_TD));
            $td1->setAttribute(self::KEY_CLASS, self::TD_LABEL);
            $td1->textContent = $label;
            
            $td2 = $tr->appendChild($doc->createElement(self::TAG_TD));         
            $td2->setAttribute(self::KEY_CLASS, self::TD_VALUE);
            $td2->textContent = isset($value) ? $value : "";
        }
        return $doc->saveHTML();
    }
    
    
    
}