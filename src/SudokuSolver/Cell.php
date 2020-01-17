<?php

namespace App\SudokuSolver;

use App\SudokuSolver\SplObjectExtended;

//Represents the cells (91 in total) in sudoku game
class Cell 
{   
    //Id format exemple '1-1-1' : first square, first row, first column 
    private $id;
    //The final result of a cell
    public $result = null;
    //Stores the wrong result sent by the user for display purpose.
    private $duplicatedNumber = null;
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
    //Result set by user will be displayed in Black
    public function setResultByRequest(int $number) 
    {   
        if($this->result !== null) {
            return;
        }
        if (!$this->integrityCheck($number)) {
            return;
        }        
        if ($this->duplicationCheck($number) !== null) {
            $this->duplicatedNumber = $number;
            return;
        } 
        //Every result setted should trigerred $this->updateParents(), but in the case of the result set by the user
        //We don't want the algorithm to find a solution by logical before all result given by the user are setted
        //Otherwise some result would be display in green while it has been sent from the user.
        $this->setFinalResult($number);
        $this->isResultSettedByUser = true;
    } 
    //Result found by the algorythm will be displayed in green
    public function setResult(int $number) 
    {   
        if($this->result !== null) {
            return;
        }
        if (!$this->integrityCheck($number)) {
            return;
        }        
        if ($this->duplicatedNumber !== null) {
            return;
        }
        if ($this->duplicationCheck($number) !== null) {
            return;
        }

        $this->setFinalResult($number);       
        $this->updateParents();
    }

    //When the result is set in the cell, it will call "updateSiblingsResultPossibilities" method from each parents, this method will then call 
    // in return "updateResultPossibilities" for each sibling cell object. It's a circular update. 
    public function updateResultPossibilities(int $indexToUpdate) : void
    {           
        if($this->result !== null) {
            return;
        }
        if ($this->duplicatedNumber !== null) {
            return;
        }        
        if (!$this->resultPossibilities[$indexToUpdate]) {
            return;
        }
       
        $this->resultPossibilities[$indexToUpdate] = false; 
        //array_sum : each value of $this->resultPossibilities will be transform in 0 if false and 1 if true.
        if(array_sum($this->resultPossibilities) == 1) {
            //setResult will also trigger updateMissingNumbers() for each parents.
            $result = array_search(true,$this->resultPossibilities);
            $this->setResult($result);
        } else {            
            foreach ($this->parents as $parent) {            
                $parent->updateMissingNumbers();            
            } 
        }        
    }

    public function updateParents() 
    {
        foreach ($this->parents as $parent) {                               
            $parent->updateCellSiblingsResultPossibilities($this);                                             
            $parent->updateMissingNumbers();               
        }   
    }    

    private function setFinalResult(int $result)
    {   
        foreach ($this->resultPossibilities as $number => $value) {
            if ($number !== $result && $value) {
                $this->resultPossibilities[$number] = false;
            }
        }

        $this->duplicatedNumber = null;
        $this->result = $result;
    }

    //Return null if the result is not duplicated in this object siblings.
    //Else it will return the current object.
    public function duplicationCheck(int $number) : ?Cell
    {
        foreach ($this->parents as $parent) {
            foreach ($parent->getCells() as $cell) {
                if($cell !== $this && $cell->getResult() == $number) {
                    return $this;
                }
            }
        }
        return null;        
    }
    
    //Comparing the result we are trying to set with the resultPossibilities.
    private function integrityCheck(int $number) : bool 
    {
        if($this->resultPossibilities[$number] == false) {
            return false;
        } else {
            return true;
        }
    }

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
    
    public function getDuplicatedNumber() : ?int
    {
        return $this->duplicatedNumber;
    }
      
    public function getIsResultSettedByUser() : bool
    {
        return $this->isResultSettedByUser;
    }

    public function clearParents() 
    {
        return $this->parents = new SplObjectExtended();
    }
}