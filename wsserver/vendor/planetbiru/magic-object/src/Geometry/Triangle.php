<?php

namespace MagicObject\Geometry;

/**
 * Class representing a Triangle with three Point objects
 */
class Triangle {

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
     * Point C
     *
     * @var Point
     */
    public $C;

    /**
     * Point a
     *
     * @var double
     */
    public $a;

    /**
     * Point b
     *
     * @var double
     */
    public $b;

    /**
     * Point c
     *
     * @var double
     */
    public $c;

    /**
     * Constructor to initialize the Triangle with three Point objects
     *
     * @param Point $A
     * @param Point $B
     * @param Point $C
     */
    public function __construct(Point $A, Point $B, Point $C) {
        $this->A = $A;
        $this->B = $B;
        $this->C = $C;

        // Calculate the lengths of the sides of the triangle
        $this->a = $B->distanceFrom($C);
        $this->b = $C->distanceFrom($A);
        $this->c = $A->distanceFrom($B);
    }

    /**
     * Method to calculate the area of the triangle using Heron's formula
     *
     * @return double
     */
    public function getArea() {
        $z = ($this->a + $this->b + $this->c) / 2;
        return sqrt($z * ($z - $this->a) * ($z - $this->b) * ($z - $this->c));
    }
}