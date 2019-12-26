<?php

namespace App\SudokuSolver;

use App\SudokuSolver\SplObjectExtended;

//Represents the cells (91 in total) in sudoku game
class Cell 
{    
    private $id;
    private $result = null;
    private $resultPossibilities = [
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
    //Object Storage for this cell's square, row and column   
    private $parents;
    
    public function __construct(string $id) 
    {  
        $this->id = $id; 
        $this->parents = new SplObjectExtended();       
    }
    //Sets the result after integrity checking then call this object's parents for update. 
    public function setResult(int $number) : void 
    {        
        if($this->result !== null) {
            return;
        } 

        if ($this->integrityCheck($number)) { 

            $this->result = $number;
        
            foreach ($this->resultPossibilities as $key => $value) {
                if ($key !== $number) {
                    $this->resultPossibilities[$key] = false;
                }
            }             

            foreach ($this->parents as $parent) {                               
                $parent->updateSiblingsResultPossibilities($this);                                          
                $parent->updateMissingNumbers(); //Each update in this object must trigger his parents objects               
            } 
        }
    }
    //Checks if the number we are trying to set has result is not duplicated in this cell's square, row and column
    private function integrityCheck(int $number) : bool 
    {
        $numberOfOccurence = 0;        
        
        foreach ($this->parents as $parent) {
            foreach ($parent->getCells() as $cell) {
                if($cell !== $this && $cell->getResult() == $number) {
                    $numberOfOccurence++;
                }
            }
        }        
        
        if($numberOfOccurence > 0) {                                   
            return false;
        } else {            
            return true;
        }
    }       
    //When the result is set in the current object, it will call "updateSiblingsResultPossibilities" method from each parents (see ligne 48 here), "updateSiblingsResultPossibilities" will then call 
    // "updateResultPossibilities" for each sibling cell object but the current one. 
    public function updateResultPossibilities(int $result) : void
    {
        $this->resultPossibilities[$result] = false;
        //Auto update itself's result if there is only one number possible in this cell regardless of his parent
        $boolIteration = 0;
        $number;
        //Each update in this object must trigger his parents objects
        foreach ($this->parents as $parent) {            
            $parent->updateMissingNumbers();            
        }  
        
        foreach ($this->resultPossibilities as $key => $value) {
            if($value) {
                $boolIteration++;
                $number = $key;
            }
        }

        if($boolIteration == 1) {
            $this->setResult($number);            
        }          
    }     
    //Todo ////////////////////////////////////////////////////////////////////
    // private function parentsUpdateSiblingsResultPossibilities($cell, $number) 
    // {
    //     $boolArray = [];
    //     $boolCount = 0;

    //     foreach ($this->parents as $parent) {                               
    //         $boolArray[$parent->getId()] = $parent->updateSiblingsResultPossibilities($cell, $number);                        
    //     } 
    //     foreach ($boolArray as $bool) {
    //         if($bool) {
    //             $boolCount++;
    //         }
    //     }
    //     if($boolCount == 3) {
    //         return true;
    //     }          
    // }
    
    public function getResult() : ?int 
    {
        return $this->result;
    }    

    public function getId() : string
    {
        return $this->id;
    }
    
    public function getResultPossibilities() : array
    {
        return $this->resultPossibilities;
    }        
  
    public function getParents() : SplObjectExtended
    {
        return $this->parents;
    }   
   
    public function setParents(Structure $parent) : void
    {
        $this->parents->attach($parent, $parent->getId());
    }
}