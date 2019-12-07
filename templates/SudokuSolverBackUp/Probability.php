<?php

namespace App\SudokuSolver;

use App\SudokuSolver\Cell;


class Probability {

    private $id;
    private $number;
    private $parent = [];
    private $cells = [];
    

    public function __construct(int $number, $parent) {
        $this->id = $number . "-" . $parent;                      
    }  

    public function setCell(Cell $cell)  
    {
        array_push($this->cell, $cell);
    }

    public function getCells() : array
    {
        return $this->cell;
    }
         
    public function getParent()
    {
        return $this->parent;
    }
    
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    public function getNumber() : int
    {
        return $this->number;
    }
    
    public function setNumber(int $number)
    {
        $this->number = $number;

        return $this;
    }
}