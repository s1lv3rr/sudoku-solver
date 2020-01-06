<?php
namespace App\SudokuSolver;

use Exception;
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
    
    public function createMissingNumbers()  
    {
        for ($i=1; $i < 10; $i++) { 
            $this->missingNumbers[$i] = new SplObjectExtended();
        }
    }
    
    //Trigerred when the grid calls Structure->setCell()
    private function populateMissingNumbers()  
    {
        foreach ($this->missingNumbers as $number) {
            foreach ($this->cells as $cell) {
                $number->attach($cell, $cell->getId());                
            }
        }
    } 
       
    //When the result is found in a Cell, it will call it's 3 Structure parents
    //Each parents will then updates it's childs
    //The Cell who called this method won't be update.
    public function updateCellSiblingsResultPossibilities(Cell $cellWithResult)  
    {   
        if ($cellWithResult->getResult() == null) {
            return;
        }

        foreach ($this->cells as $cell) {
            //If cell->result is not null or already up to date, $cell->updateResultPossibilities will return
            if ($cell !== $cellWithResult) {
                $cell->updateResultPossibilities($cellWithResult->getResult());                
            }
        }        
    }

    //Called everytime a child's (Cell) resultPossibilites is updated
    public function updateMissingNumbers() 
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
        $this->uniqueCandidateRule();
        $this->squareToRowOrColumnRule();
        //$this->nakedPairRule();  Work In Progress                
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

    //Clear already found missing numbers
    private function clearEmptyMissingNumbers() 
    {
        foreach ($this->cells as $cell) {            
            if ($cell->getResult() !== null) {
                unset($this->missingNumbers[$cell->getResult()]);
            }            
        }
    }

    //If a $this->missingNumbers entry has only one Cell object in it's storage, the result is found.
    private function uniqueCandidateRule()
    {
        foreach ($this->missingNumbers as $number => $splObject) {
            if ($splObject->count() == 1) {
                foreach ($splObject as $cell) {
                    $cell->setResult($number);
                }
            }
        }
    }

    private function squareToRowOrColumnRule()
    {
        foreach ($this->missingNumbers as $number => $splObject) {

            if($splObject->count() == 2 || $splObject->count() == 3) {

                $haveTheyACommonParent = [];
                
                foreach ($splObject as $cell) {                    
                    array_push($haveTheyACommonParent, $cell);                       
                } 
                            
                $commonParent = $this->getSecondSimilarParent($haveTheyACommonParent);
                
                if($commonParent !== null) {                                       
                    $this->updateCommonParent($commonParent, $number);                                        
                }                                
            }            
        }
    }

    //Return the other parent in common of an array of 2 or 3 cells or Null
    private function getSecondSimilarParent(array $cellsToCompare) : ?Structure 
    {           
        if (count($cellsToCompare) < 2 || count($cellsToCompare) > 3) {
            throw new Exception('Array length must be 2 or 3.');
        } 
        
        foreach ($cellsToCompare as $cell) {
            if (!$cell instanceof Cell) {
                throw new Exception('Elements must be of type Cell.');
            }
        }
        
        $firstCell = $cellsToCompare[0];
        $firstCellParents = [];
        
        foreach ($firstCell->getParents() as $parent) {
            if ($parent !== $this) {
                array_push($firstCellParents, $parent);
            }
        }               
        
        $firstParentCount = 0;
        $secondParentCount = 0;
        
        foreach ($cellsToCompare as $cell) {
            foreach ($cell->getParents() as $parent) {                                                                                  
                if ($parent == $firstCellParents[0]) {
                    $firstParentCount++;
                }
                if ($parent == $firstCellParents[1]) {
                    $secondParentCount++;
                }        
            }
        }
        //Because the array will contains all parents BUT NOT the current object
        //if the parent count is equal to $cellsToCompare array lenght, there is another parent in common       
        if ($firstParentCount == count($cellsToCompare)) {
            return $firstCellParents[0];
        }
        if ($secondParentCount == count($cellsToCompare)) {
            return $firstCellParents[1];
        }
        return null;
    } 

    //Updates the Cells of the common parent.
    //But only the cells that the 2 parents don't have in common
    private function updateCommonParent($commonParent, $numberToUpdate) 
    {    
        foreach ($commonParent->getCells() as $cell) {            
            if (!$cell->getParents()->contains($this)) {
                $cell->updateResultPossibilities($numberToUpdate);
            }
        }        
    }    

    public function setCell(Cell $cell) 
    {
        $this->cells += [$cell->getId() => $cell];
        $this->populateMissingNumbers();
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

    public function clearCells() 
    {
        return $this->cells = [];
    }

    //WORK IN PROGRESS
    // private function nakedPairRule() 
    // {   
    //     $nakedPair = $this->findNakedPairs(); //Return an array of 2 or Null

    //     if ($nakedPair !== null && !in_array($nakedPair,$this->nakedPairs)) {
            
    //         $firstPair = array_slice($nakedPair,0,1);
    //         $secondPair = array_slice($nakedPair,1);
            
    //         foreach ($this->cells as $cell) {
    //             if ($cell->getId() !== array_key_first($firstPair) && $cell->getId() !== array_key_first($secondPair)) {                    
    //                 foreach ($firstPair as $cellId => $arrayOfNumbers) {                        
    //                     foreach ($arrayOfNumbers as $index => $number) {                            
    //                         $cell->updateResultPossibilities($number);
    //                     }                        
    //                 }
    //             }
    //         }
    //     }     
        
    //     array_push($this->nakedPairs, $nakedPair);
    // }

    //Will find 1 naked pairs if there is one, if there is more, they will be ignored
    //Not optimal....
    //A naked pair is 2 cells with only 2 resultPossibilities in common
    // private function findNakedPairs() : ?array
    // {   
    //     $cellsIdsAndPossibleNumbers = [];
    //     $pairs = [];
        
    //     foreach ($this->cells as $cell) {

    //         $cellsIdsAndPossibleNumbers[$cell->getId()] = [];

    //         foreach ($cell->getResultPossibilities() as $number => $bool) {
    //             if ($bool) {
    //                 array_push($cellsIdsAndPossibleNumbers[$cell->getId()], $number);
    //             }
    //         }             
    //     }

    //     foreach ($cellsIdsAndPossibleNumbers as $index =>$array) {
    //         if (count($array) !== 2) {
    //             unset($cellsIdsAndPossibleNumbers[$index]); //If the cell have more than 2 resultPossibilities
    //         }
    //     }

    //     //Double loop on the same array to find out if there is two elements with the same value but different id's         
    //     foreach ($cellsIdsAndPossibleNumbers as $cellId => $numberArray) {                
    //         foreach ($cellsIdsAndPossibleNumbers as $cellId2 => $numberArray2) {                    
    //             if ($numberArray == $numberArray2 && $cellId !== $cellId2) {
    //                 $pairs[$cellId] = [];
    //                 $pairs[$cellId] = $numberArray;
    //             }                    
    //         }
    //     }       

    //     if (!empty($pairs)) {                        
    //         return $pairs;
    //     } else {
    //         return null;
    //     }                
    // }
}

