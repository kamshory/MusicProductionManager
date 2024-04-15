<?php

namespace MagicObject\Geometry;

/**
 * Class representing a Line with two Point objects
 */
class Line {

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

    /**
     * Constructor to initialize the Line with two Point objects
     *
     * @param Point $A
     * @param Point $B
     */
    public function __construct(Point $A, Point $B) {
        $this->A = $A;
        $this->B = $B;
    }

    /**
     * Method to calculate the length of the line
     *
     * @return double
     */
    public function getLength() {
        return $this->A->distanceFrom($this->B);
    }
}