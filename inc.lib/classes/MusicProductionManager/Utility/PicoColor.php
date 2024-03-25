<?php

namespace MusicProductionManager\Utility;

class PicoColor
{
    public $red = 255;
    public $green = 255;
    public $blue = 255;

    /**
     * Constructor
     *
     * @param integer $red
     * @param integer $green
     * @param integer $blue
     */
    public function __construct($red, $green, $blue)
    {
        $this->red = $red;
        $this->green = $green;
        $this->blue = $blue;  
    }

    /**
     * Static constructor
     *
     * @param integer $red
     * @param integer $green
     * @param integer $blue
     * @return self
     */
    public static function valueOf($red, $green, $blue)
    {
        return new PicoColor($red, $green, $blue);
    }

    /**
     * Get the value of red
     */ 
    public function getRed()
    {
        return $this->red;
    }

    /**
     * Set the value of red
     *
     * @return  self
     */ 
    public function setRed($red)
    {
        $this->red = $red;

        return $this;
    }

    /**
     * Get the value of green
     */ 
    public function getGreen()
    {
        return $this->green;
    }

    /**
     * Set the value of green
     *
     * @return  self
     */ 
    public function setGreen($green)
    {
        $this->green = $green;

        return $this;
    }

    /**
     * Get the value of blue
     */ 
    public function getBlue()
    {
        return $this->blue;
    }

    /**
     * Set the value of blue
     *
     * @return  self
     */ 
    public function setBlue($blue)
    {
        $this->blue = $blue;

        return $this;
    }
    
    /**
     * Convert object to string
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf("#%2X%2X%2X", $this->red, $this->green, $this->blue);
    }
}
