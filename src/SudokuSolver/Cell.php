<?php

namespace App\SudokuSolver;

use App\SudokuSolver\SplObjectExtended;

//Represents the cells (91 in total) in sudoku game
class Cell 
{   
    //Id format exemple '1-1-1' : first square, first row, first column 
    private $id;
    //The final result of a cell
    private $result = null;
    //Stores the wrong result sent by the user for display purpose.
    private $wrongResult = null;
    //Indicates if the result has been sent by the user for display purpose.
    private $isResultSettedByUser = false;    
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
    //Object Storage for the cell's square, row and column   
    private $parents;    
    
    public function __construct(string $id) 
    {  
        $this->id = $id; 
        $this->parents = new SplObjectExtended();       
    }
    //Handles the results sent by the user. 
    //Result sent by user will be display in a different color than the result found by this program
    public function setResultByUser(int $number) {

        $integrityCheck = $this->integrityCheck($number);

        if (!$integrityCheck InstanceOf Cell) {

            $setResult = $this->setResult($number);

            if ($setResult) {
                $this->isResultSettedByUser = true;
            }

        } else {

            $this->wrongResult = $number;            
        }
    }
    
    public function setResult(int $number) : bool 
    {        
        if($this->result !== null) {
            return false;
        } 

        $integrityCheck = $this->integrityCheck($number);

        if (!$integrityCheck InstanceOf Cell) { 

            $this->result = $number;            
            $this->wrongResult = null;
        
            foreach ($this->resultPossibilities as $key => $value) {
                if ($key !== $number) {
                    $this->resultPossibilities[$key] = false;
                }
            }             

            foreach ($this->parents as $parent) {                               
                $parent->updateSiblingsResultPossibilities($this); //Each parents then will call $this->updateResultPossibilities()                                            
                $parent->updateMissingNumbers(); //Everytime resultPossibilities is updated, this object parents must be updated               
            }

            return true;

        } else {

            return false;
        } 
    }
    //Return true if the result is not duplicated in this object siblings.
    //Else it will return the current object.
    private function integrityCheck(int $number) 
    {
        $numberOfOccurence = 0;        
        
        foreach ($this->parents as $parent) {
            foreach ($parent->getCells() as $cell) {
                if($cell !== $this && $cell->getResult() == $number) {
                    return $this;
                }
            }
        } 

        return true;        
    }       
    //When the result is set in the cell, it will call "updateSiblingsResultPossibilities" method from each parents, this method will then call 
    // in return "updateResultPossibilities" for each sibling cell object. It's a circular update. 
    public function updateResultPossibilities(int $indexToUpdate) : void
    {   
        if ($this->getResult() == null) {

            $this->resultPossibilities[$indexToUpdate] = false;            
            //Everytime resultPossibilities is updated, this object parents must be updated 
            foreach ($this->parents as $parent) {            
                $parent->updateMissingNumbers();            
            } 
            
            //This object can auto update itselfs if there is only one result possible
            $boolIteration = 0;
            $result;
            
            foreach ($this->resultPossibilities as $number => $bool) {
                if($bool) {
                    $boolIteration++;
                    $result = $number;
                }
            }

            if($boolIteration == 1) {
                $this->setResult($result);            
            }

        } else {
            return;
        }
        
    }
    
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
    
    public function getWrongResult() : ?int
    {
        return $this->wrongResult;
    }
      
    public function getIsResultSettedByUser() : bool
    {
        return $this->isResultSettedByUser;
    }
}