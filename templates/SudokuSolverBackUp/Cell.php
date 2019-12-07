<?php

namespace App\SudokuSolver;

class Cell {
    
    private $id;
    private $number = null;
    private $probabilities = [
        1 => true,
        2 => true,
        3 => true,
        4 => true,
        5 => true,
        6 => true,
        7 => true,
        8 => true,
        9 => true,
    ];
    private $square;
    private $row;
    private $column;

    public function __construct(string $id){
        $this->id = $id;
    }

    public function setNumber(int $number) {

        $this->number = $number;
        
        foreach($this->probabilities as $key => $value) {            
            
            if($key !== $number) {                
                $this->probabilities[$key] = false;
            }
        }
    }

    public function getNumber() : ?int {

        return $this->number;        
        
    }
    
    public function getSquare() : Square
    {
        return $this->square;
    }

    public function setSquare($square)
    {
        $this->square = $square;

        return $this;
    }
    
    public function getRow() : Row
    {
        return $this->row;
    }
    
    public function setRow($row)
    {
        $this->row = $row;

        return $this;
    }
    
    public function getColumn() : Column
    {
        return $this->column;
    }
   
    public function setColumn($column)
    {
        $this->column = $column;

        return $this;
    }

    public function getId() : string
    {
        return $this->id;
    }

    /**
     * Get the value of probabilities
     */ 
    public function getProbabilities()
    {
        return $this->probabilities;
    }

    /**
     * Set the value of probabilities
     *
     * @return  self
     */ 
    public function updateProbabilities(int $number)
    {
        $this->probabilities[$number] = false;

        return $this;
    }
}