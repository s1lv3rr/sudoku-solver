<?php
namespace App\SudokuSolver;

use App\SudokuSolver\SplObjectExtended;

//Represents the squares, rows and columns in sudoku game
class Structure 
{   
    //Id exemples : 'Square 1', 'Row 2' ect ect
    protected $id;    
    protected $cells = [];    
    protected $missingNumbers = [];
    
    public function __construct(string $structure, int $id) 
    {
        $this->id = $structure . ' ' . $id; 
        $this->createMissingNumbers();            
    }
    
    private function createMissingNumbers() : void 
    {
        for ($i=1; $i < 10; $i++) { 
            $this->missingNumbers[$i] = new SplObjectExtended();
        }
    }
    //Populate the missingNumbers array with all this structure's child
    //Trigerred when the grid calls this class "setCell" method
    private function attachChilds() : void 
    {
        foreach ($this->missingNumbers as $number) {
            foreach ($this->cells as $cell) {
                $number->attach($cell, $cell->getId());
            }
        }
    }   
    //When a result is updated in a Cell, it will call it's 3 Structure parents
    //Each parents will then updates it's childs
    //The Cell who called this update won't be updated as well avoiding unecessary work.
    public function updateSiblingsResultPossibilities(Cell $cell) : void 
    {        
        foreach ($this->cells as $currentCell) {
            if ($currentCell !== $cell && $currentCell->getResult() == null) {
                $currentCell->updateResultPossibilities($cell->getResult());                
            }
        }
        
    }
    
    public function updateMissingNumbers() : void 
    {
        $this->clearEmptyMissingNumbers();
        $this->clearMissingNumbersFromResolvedCells();

        foreach ($this->cells as $cell) {
            foreach ($cell->getResultPossibilities() as $number => $value) {
                foreach ($this->missingNumbers as $key => $splObject) {
                    if ($key == $number) {                            
                        if (!$value) {
                            $splObject->detach($cell);
                        }                                     
                    }
                }
            }
        }
        $this->solve();
    }
    //Every Cell objects with a result must be unset (detach) from the object storages
    private function clearMissingNumbersFromResolvedCells() 
    {
        foreach ($this->missingNumbers as $key => $splObject) {
            foreach ($splObject as $cell) {
                if ($cell->getResult() !== null) {
                    $splObject->detach($cell);
                }
            }
        }
    }
    //If an object storage is empty in $this->missingNumbers, this means that the result has been found for this particulary index 
    private function clearEmptyMissingNumbers() 
    {
        foreach ($this->cells as $cell) {            
            if ($cell->getResult() !== null) {
                unset($this->missingNumbers[$cell->getResult()]);
            }            
        }
    }
    //If a $this->missingNumbers entry has only one Cell object in it's storage, the result is found.
    private function solve() : void 
    {           
        foreach ($this->missingNumbers as $key => $splObject) {
            if ($splObject->count() == 1) {
                foreach ($splObject as $cell) {
                    $cell->setResult($key);
                }
            }
            // if($splObject->count() == 2 || $splObject->count() == 3) {

            //     $cellsArrayToCheck = [];
            //     foreach ($splObject as $cell) {                    
            //         array_push($cellsArrayToCheck, $cell);                       
            //     } 
                
            //     $commonParent = $this->getSecondSimilarParent($cellsArrayToCheck);                    
            //     if($commonParent !== null) {                            
            //         $this->updateCommonParent($commonParent, $key);                        
            //     }                                
            // }
        }
    }

    public function setCell(Cell $cell) : void 
    {
        $this->cells += [$cell->getId() => $cell];
        $this->attachChilds();
    }

    public function getCells() : array 
    {
        return $this->cells;
    }     
   
    public function getId() : ?string
    {
        return $this->id;
    }
        
    public function getMissingNumbers() : array
    {
        return $this->missingNumbers;
    }

    //Work in progress----------------------------------------------------////
    
    // private function getSecondSimilarParent(array $cellsArray) : ?Structure 
    // {        
    //     $firstCell = $cellsArray[0];
    //     $firstCellParents = [];
        
    //     foreach ($firstCell->getParents() as $parent) {
    //         if ($parent !== $this) {
    //             array_push($firstCellParents, $parent);
    //         }
    //     }   
    //     unset($parent);        
            
    //     $firstParentCount = 0;
    //     $secondParentCount = 0;

    //     foreach ($cellsArray as $cell) {
    //         foreach ($cell->getParents() as $parent) {                                                                                  
    //             if ($parent == $firstCellParents[0]) {
    //                 $firstParentCount++;
    //             }
    //             if ($parent == $firstCellParents[1]) {
    //                 $secondParentCount++;
    //             }        
    //         }
    //     } 
        
    //     if ($firstParentCount == count($cellsArray)) {
    //         return $firstCellParents[0];
    //     }
    //     if ($secondParentCount == count($cellsArray)) {
    //         return $firstCellParents[1];
    //     }
    //     return null;
    // }   

    // private function updateCommonParent($parent, $number) 
    // {             
    //     foreach ($parent->getCells() as $cell) {
    //         if ($cell->getResult() == null) {
    //             $cell->updateResultPossibilities($number);
    //         }            
    //     }
    // }
     
    
}