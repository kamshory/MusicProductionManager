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
     * @param string $align
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
     * Set text
     *
     * @param string $text
     * @return self
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }
}

