<?php
namespace App\SudokuSolver;

use App\SudokuSolver\SplObjectExtended;

//Represents the squares, rows and columns in sudoku game
class Structure 
{
    protected $id;    
    protected $cells = [];    
    protected $missingNumbers = [];
    
    public function __construct(string $structure, int $id) 
    {
        $this->id = $structure . ' ' . $id; 
        $this->createMissingNumbers();            
    }
    
    protected function createMissingNumbers() : void 
    {
        for ($i=1; $i < 10; $i++) { 
            $this->missingNumbers[$i] = new SplObjectExtended();
        }
    }

    protected function attachChilds() : void 
    {
        foreach ($this->missingNumbers as $number) {
            foreach ($this->cells as $cell) {
                $number->attach($cell, $cell->getId());
            }
        }
    }   
   
    public function updateSiblingsResultPossibilities(Cell $cell) : void 
    {        
        foreach ($this->cells as $currentCell) {
            if($currentCell !== $cell && $currentCell->getResult() == null) {
                $currentCell->updateResultPossibilities($cell->getResult());                
            }
        }
        
    }

    public function initialiseMissingNumbers() : void 
    {        
        foreach ($this->cells as $cell) {
            foreach ($cell->getResultPossibilities() as $number => $value) {
                foreach ($this->missingNumbers as $key => $splObject) {
                    if($key == $number) {
                        if($value){                       
                            $splObject->attach($cell);                             
                        }                              
                    }
                }
            }
        }        
    }

    public function updateMissingNumbers() : void 
    {
        $this->clearEmptySplObject();
        $this->clearCellsWithResultFromMissingNumbers();

        foreach ($this->cells as $cell) {
            foreach ($cell->getResultPossibilities() as $number => $value) {
                foreach ($this->missingNumbers as $key => $splObject) {
                    if($key == $number) {                            
                        if(!$value) {
                            $splObject->detach($cell);
                        }                                     
                    }
                }
            }
        }
        $this->resolve();
    }

    private function resolve() : void 
    {
        foreach ($this->missingNumbers as $key => $splObject) {
            if($splObject->count() == 1) {
                foreach ($splObject as $cell) {
                    $cell->setResult($key);
                }
            }
            // if($splObject->count() == 2 || $splObject->count() == 3) {                
            //     $cellsArray = [];
            //     foreach ($splObject as $cell) {                    
            //         array_push($cellsArray, $cell);                       
            //     }   
            //     if($this->getId() == 'Row 5') {
            //         $commonParent = $this->getSecondSimilarParent($cellsArray);                    
            //         if($commonParent !== null) {
                        //$this->updateCommonParent($commonParent, $key);
                        // dump($this->cells);
                        // dump($this->missingNumbers);
                        // dump($key);
                        // dump($cellsArray);
                        // dump($commonParent);
                        // exit;
            //         } 
            //     }               
            // }
        }
    }
    
    // private function getSecondSimilarParent(array $cellsArray) : ?Structure {
        
    //     $firstCell = $cellsArray[0];
    //     $firstCellParents = [];
        
    //     foreach ($firstCell->getParents() as $parent) {
    //         if($parent !== $this) {
    //             array_push($firstCellParents, $parent);
    //         }
    //     }   
    //     unset($parent);        
            
    //     $firstParentCount = 0;
    //     $secondParentCount = 0;

    //     foreach ($cellsArray as $cell) {
    //         foreach ($cell->getParents() as $parent) {                                                                                  
    //             if($parent == $firstCellParents[0]) {
    //                 $firstParentCount++;
    //             }
    //             if($parent == $firstCellParents[1]) {
    //                 $secondParentCount++;
    //             }        
    //         }
    //     } 
        
    //     if($firstParentCount == count($cellsArray)) {
    //         return $firstCellParents[0];
    //     }
    //     if($secondParentCount == count($cellsArray)) {
    //         return $firstCellParents[1];
    //     }
    //     return null;
    // }

    private function clearCellsWithResultFromMissingNumbers() 
    {
        foreach ($this->missingNumbers as $key => $splObject) {
            foreach ($splObject as $cell) {
                if($cell->getResult() !== null) {
                    $splObject->detach($cell);
                }
            }
        }
    }

    private function clearEmptySplObject() 
    {
        foreach ($this->missingNumbers as $key => $splObject) {            
            if($splObject->count() == 0) {
                unset($this->missingNumbers[$key]);
            }            
        }
    }

    // private function updateCommonParent($parent, $number) { 
            
    //     foreach ($parent->getCells() as $cell) {            
    //         $cell->updateResultPossibilities($number);
    //     }
    // }
     
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
}