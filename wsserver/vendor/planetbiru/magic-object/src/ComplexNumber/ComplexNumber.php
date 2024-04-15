<?php
namespace MagicObject\ComplexNumber;

class ComplexNumber { 

    /**
     * Real number
     *
     * @var double
     */
    private $real; 

    /**
     * Imaginary number
     *
     * @var double
     */
    private $imaginary; 
  
    /**
     * Constructor
     *
     * @param double $real
     * @param double $imaginary
     */
    public function __construct($real, $imaginary) { 
        $this->real = $real; 
        $this->imaginary = $imaginary; 
    } 
  
    /**
     * Add complex number
     *
     * @param self $complexNumber
     * @return self
     */
    public function add(ComplexNumber $complexNumber) { 
        return new ComplexNumber( 
            $this->real + $complexNumber->getReal(), 
            $this->imaginary + $complexNumber->getImaginary() 
        ); 
    } 
  
    /**
     * Subtract complex number
     *
     * @param self $complexNumber
     * @return self
     */
    public function subtract(ComplexNumber $complexNumber) { 
        return new ComplexNumber( 
            $this->real - $complexNumber->getReal(), 
            $this->imaginary - $complexNumber->getImaginary() 
        ); 
    } 
  
    /**
     * Multiply complex number
     *
     * @param self $complexNumber
     * @return self
     */
    public function multiply(ComplexNumber $complexNumber) { 
        $real = $this->real * $complexNumber->getReal()  
            - $this->imaginary * $complexNumber->getImaginary(); 
              
        $imaginary = $this->real * $complexNumber->getImaginary()  
            + $this->imaginary * $complexNumber->getReal(); 
              
        return new ComplexNumber($real, $imaginary); 
    } 
  
    public function __toString() { 
        return "({$this->real}, {$this->imaginary}i)"; 
    } 
  
    

    /**
     * Get real number
     *
     * @return double
     */ 
    public function getReal()
    {
        return $this->real;
    }

    /**
     * Set real number
     *
     * @param double $real Real number
     *
     * @return self
     */ 
    public function setReal($real)
    {
        $this->real = $real;

        return $this;
    }

    /**
     * Get imaginary number
     *
     * @return double
     */ 
    public function getImaginary()
    {
        return $this->imaginary;
    }

    /**
     * Set imaginary number
     *
     * @param double $imaginary Imaginary number
     *
     * @return self
     */ 
    public function setImaginary($imaginary)
    {
        $this->imaginary = $imaginary;

        return $this;
    }
} 