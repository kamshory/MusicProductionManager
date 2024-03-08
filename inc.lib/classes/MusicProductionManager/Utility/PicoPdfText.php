<?php

namespace MusicProductionManager\Utility;

class PicoPdfText
{
    /**
     * X coordinate
     *
     * @var float
     */
    public $x = 0.0;

    /**
     * Y coordinate
     *
     * @var float
     */
    public $y = 0.0;

    /**
     * Width
     *
     * @var float
     */
    public $width = 10.0;

    /**
     * Height
     *
     * @var float
     */
    public $height = 10.0;

    /**
     * Text
     *
     * @var string
     */
    public $text = "";

    /**
     * Border
     *
     * @var integer
     */
    public $border = 0;

    /**
     * Fill
     *
     * @var integer
     */
    public $fill = 0;

    /**
     * Align
     *
     * @var string
     */
    public $align = "C";

    /**
     * Font name
     *
     * @var string
     */
    public $fontName = "Helvetica";

    /**
     * Font size
     *
     * @var integer
     */
    public $fontSize = 16;

    /**
     * Fill color
     *
     * @var PicoColor
     */
    public $fillColor = null;

    /**
     * Text color
     *
     * @var PicoColor
     */
    public $textColor = null;

    public function __construct()
    {
        $this->fillColor = PicoColor::valueOf(255, 255, 255);
        $this->textColor = PicoColor::valueOf(0, 0, 0);
    }
    /**
     * Set position
     *
     * @param float $x
     * @param float $y
     * @return self
     */
    public function setPosition($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
        return $this;
    }

    /**
     * Set dimension
     *
     * @param float $width
     * @param float $height
     * @return self
     */
    public function setDimension($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
        return $this;
    }

    /**
     * Set style
     *
     * @param integer $border
     * @param integer $fill
     * @param $align
     * @return self
     */
    public function setStyle($border, $fill, $align)
    {
        $this->border = $border;
        $this->fill = $fill;
        $this->align = $align;
        return $this;
    }

    /**
     * Align center
     *
     * @return self
     */
    public function alignCenter()
    {
        $this->x = $this->x - ($this->width / 2);
        $this->align = "C";
        return $this;
    }

    /**
     * Align right
     *
     * @return self
     */
    public function alignRight()
    {
        $this->x = $this->x - $this->width ;
        $this->align = "R";
        return $this;
    }

    /**
     * Align left
     *
     * @return self
     */
    public function alignLeft()
    {
        $this->align = "L";
        return $this;
    }

    /**
     * Get x coordinate
     *
     * @return  float
     */ 
    public function getX()
    {
        return $this->x;
    }

    /**
     * Set x coordinate
     *
     * @param  float  $x  X coordinate
     *
     * @return  self
     */ 
    public function setX(float $x)
    {
        $this->x = $x;

        return $this;
    }

    /**
     * Get y coordinate
     *
     * @return  float
     */ 
    public function getY()
    {
        return $this->y;
    }

    /**
     * Set y coordinate
     *
     * @param  float  $y  Y coordinate
     *
     * @return  self
     */ 
    public function setY(float $y)
    {
        $this->y = $y;

        return $this;
    }

    /**
     * Get width
     *
     * @return  float
     */ 
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set width
     *
     * @param  float  $width  Width
     *
     * @return  self
     */ 
    public function setWidth(float $width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get height
     *
     * @return  float
     */ 
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set height
     *
     * @param  float  $height  Height
     *
     * @return  self
     */ 
    public function setHeight(float $height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get text
     *
     * @return  string
     */ 
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set text
     *
     * @param  string  $text  Text
     *
     * @return  self
     */ 
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get border
     *
     * @return  integer
     */ 
    public function getBorder()
    {
        return $this->border;
    }

    /**
     * Set border
     *
     * @param  integer  $border  Border
     *
     * @return  self
     */ 
    public function setBorder($border)
    {
        $this->border = $border;

        return $this;
    }

    /**
     * Get fill
     *
     * @return  integer
     */ 
    public function getFill()
    {
        return $this->fill;
    }

    /**
     * Set fill
     *
     * @param  integer  $fill  Fill
     *
     * @return  self
     */ 
    public function setFill($fill)
    {
        $this->fill = $fill;

        return $this;
    }

    /**
     * Get align
     *
     * @return  string
     */ 
    public function getAlign()
    {
        return $this->align;
    }

    /**
     * Set align
     *
     * @param  string  $align  Align
     *
     * @return  self
     */ 
    public function setAlign($align)
    {
        $this->align = $align;

        return $this;
    }

    /**
     * Get font name
     *
     * @return  string
     */ 
    public function getFontName()
    {
        return $this->fontName;
    }

    /**
     * Set font name
     *
     * @param  string  $fontName  Font name
     *
     * @return  self
     */ 
    public function setFontName($fontName)
    {
        $this->fontName = $fontName;

        return $this;
    }

    /**
     * Get font size
     *
     * @return  integer
     */ 
    public function getFontSize()
    {
        return $this->fontSize;
    }

    /**
     * Set font size
     *
     * @param  integer  $fontSize  Font size
     *
     * @return  self
     */ 
    public function setFontSize($fontSize)
    {
        $this->fontSize = $fontSize;

        return $this;
    }

    /**
     * Get fill color
     *
     * @return  PicoColor
     */ 
    public function getFillColor()
    {
        return $this->fillColor;
    }

    /**
     * Set fill color
     *
     * @param  PicoColor  $fillColor  Fill color
     *
     * @return  self
     */ 
    public function setFillColor(PicoColor $fillColor)
    {
        $this->fillColor = $fillColor;

        return $this;
    }

    /**
     * Get text color
     *
     * @return  PicoColor
     */ 
    public function getTextColor()
    {
        return $this->textColor;
    }

    /**
     * Set text color
     *
     * @param  PicoColor  $textColor  Text color
     *
     * @return  self
     */ 
    public function setTextColor(PicoColor $textColor)
    {
        $this->textColor = $textColor;

        return $this;
    }
}

