<?php

namespace App\SudokuSolver;

use App\SudokuSolver\Cell;

//A probability object contain a number, a parent and one or several cell object.
//For example : number 8 could be in 3 differents cells in a particular row.
//Number is 8, parent is the row, cells array will contain 3 cell objects.
//If a probability object contain only one cell in it's array, then 8 is the result for this cell.-,,,,,,
class Probability {

    private $id;
    private $number;
    //a square, row or column
    private $parent;
    private $cells = [];    
    

    public function __construct(int $number, $parent) {
        $this->id = $number . $parent->getId(); 
        $this->number = $number;
        $this->parent = $parent;                    
    } 

    //Storing the cells with their IDs is necessary for the unset function above.
    public function setCell(Cell $cell) {
        $this->cells += [$cell->getId() => $cell];
    }

    public function getCells() : array
    {
        return $this->cells;
    }
    
    public function unsetCell(Cell $cell) {        
        unset($this->cells[$cell->getId()]);
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