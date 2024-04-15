<?php

namespace MagicObject\Geometry;

use MagicObject\Exceptions\InvalidPolygonException;

/**
 * Class representing a Line with two Point objects
 */
class Rectangle {

    /**
     * Point A
     *
     * @var Point
     */
    public $A;

    /**
     * Point B
     *
     * @var Point
     */
    public $B;

    public $polygon = new Polygon();

    /**
     * Constructor to initialize the Rectangle with two Point objects
     *
     * @param Point $A
     * @param Point $B
     */
    public function __construct(Point $A, Point $B) {
        $this->A = $A;
        $this->B = $B;

        $point1 = new Point($this->A->x, $this->A->y);
        $point2 = new Point($this->B->x, $this->A->y);
        $point3 = new Point($this->B->x, $this->B->y);
        $point4 = new Point($this->A->x, $this->B->y);
        
        $this->polygon
            ->addPoint($point1)
            ->addPoint($point2)
            ->addPoint($point3)
            ->addPoint($point4);
        
    }

    /**
     * Function to calculate the area of a polygon using the Shoelace formula
     *
     * @return double
     * @throws InvalidPolygonException
     */
    public function getArea()
    {
        return $this->polygon->getArea();
    }

    /**
     * Get circumference
     *
     * @return double
     * @throws InvalidPolygonException
     */
    public function getCircumference()
    {
        return $this->polygon->getCircumference();
    }
}