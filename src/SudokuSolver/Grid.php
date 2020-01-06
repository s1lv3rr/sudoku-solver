<?php

namespace App\SudokuSolver;

use Exception;
use App\SudokuSolver\Cell;
use App\SudokuSolver\Structure;

//Initialize and manage all squares, rows and columns along with the cells.
class Grid 
{   
    //Array of Structure object, structure are the squares, rows and columns.
    //In the Cell class, they are refered as "parents", Cells are the Structures's childs     
    private $structures = [];
    private $cells = [];
    private $structuresClones = [];
    private $cellsClones = [];
    private $isCompleted = false;      

    public function __construct(array $request) 
    {        
        $this->createStructures();
        $this->createCells();
        $this->mapping($this->cells, $this->structures);
        $this->setResultsFromRequest($request);
        $this->solve();

        $isGridCompleted = $this->isGridCompleted($this->cells);
        
        if (!$isGridCompleted) {

            $this->cloneGrid();
            $tryToSolve = $this->tryToSolve(); 
            
            if (!$tryToSolve) {
                $this->isCompleted = false;
            } else {
                $this->isCompleted = true;
            }
        } else {
            $this->isCompleted = true;
        }          
    }

    //Creates 27 parents objects (9 squares, 9 rows and 9 columns) and store them in this object "structures" property    
    private function createStructures() 
    {
        for ($i=1; $i <10; $i++) {
            $square = new Structure('Square',$i); //The object id is sent to the construct           
            $row = new Structure('Row',$i);            
            $column = new Structure('Column',$i);        
            $this->structures += [$square->getId() => $square];
            $this->structures += [$row->getId() => $row];
            $this->structures += [$column->getId() => $column];
        }
    } 

    //Creates the 81 Cell objects in total
    private function createCells() 
    {
        for ($square=1; $square < 10; $square++) { 
            if ($square < 4) {
                //Square 1 to 3 share the same rows                 
                for ($row = 1; $row < 4; $row++) {                    
                    if ($square == 1) {
                        //But each squares in the 1 to 3 range have differents column                         
                        for ($column = 1; $column < 4; $column++){
                            $cell = new Cell($square . "-" . $row . "-" . $column);
                            $this->cells[$cell->getId()] = $cell;                                      
                        }                                                                   
                    }
                    if ($square == 2) {
                        for ($column = 4; $column < 7; $column++){                            
                            $cell = new Cell($square . "-" . $row . "-" . $column);
                            $this->cells[$cell->getId()] = $cell;
                        }                                                                      
                    }
                    if ($square == 3) {
                        for ($column = 7; $column < 10; $column++){
                            $cell = new Cell($square . "-" . $row . "-" . $column);
                            $this->cells[$cell->getId()] = $cell; 
                        }                                                                       
                    }
                }         
            }
            if ($square < 7) {                 
                for ($row = 4; $row < 7; $row++) {                    
                    if ($square == 4) {                        
                        for ($column = 1; $column < 4; $column++){
                            $cell = new Cell($square . "-" . $row . "-" . $column);
                            $this->cells[$cell->getId()] = $cell;                                      
                        }                                                                   
                    }
                    if ($square == 5) {
                        for ($column = 4; $column < 7; $column++){                            
                            $cell = new Cell($square . "-" . $row . "-" . $column);
                            $this->cells[$cell->getId()] = $cell;
                        }                                                                      
                    }
                    if ($square == 6) {
                        for ($column = 7; $column < 10; $column++){
                            $cell = new Cell($square . "-" . $row . "-" . $column);
                            $this->cells[$cell->getId()] = $cell; 
                        }                                                                       
                    }
                }         
            }        
            if ($square < 10) {                 
                for ($row = 7; $row < 10; $row++) {                    
                    if ($square == 7) {                        
                        for ($column = 1; $column < 4; $column++){
                            $cell = new Cell($square . "-" . $row . "-" . $column);
                            $this->cells[$cell->getId()] = $cell;                                      
                        }                                                                   
                    }
                    if ($square == 8) {
                        for ($column = 4; $column < 7; $column++){                            
                            $cell = new Cell($square . "-" . $row . "-" . $column);
                            $this->cells[$cell->getId()] = $cell;
                        }                                                                      
                    }
                    if ($square == 9) {
                        for ($column = 7; $column < 10; $column++){
                            $cell = new Cell($square . "-" . $row . "-" . $column);
                            $this->cells[$cell->getId()] = $cell; 
                        }                                                                       
                    }
                }         
            }                
        }
    }

    //Map the rights Cells in the right Structures and the other way around
    //Cell's id format exemple '1-1-1' : first square, first row, first column
    //NB : $structure->setCell() also triggers $structure->populateMissingNumbers();  
    private function mapping($cellsArray, $structuresArray)
    {
        foreach ($cellsArray as $cell) {
            $cellExplodedId = explode("-",$cell->getId());
            foreach ($structuresArray as $structure) {
                if ($structure->getId() == "Square ".$cellExplodedId[0]) {
                    $structure->setCell($cell);                    
                    $cell->setParents($structure);                                       
                }
                if ($structure->getId() == "Row ".$cellExplodedId[1]) {
                    $structure->setCell($cell);                     
                    $cell->setParents($structure);                                    
                }
                if ($structure->getId() == "Column ".$cellExplodedId[2]) {
                    $structure->setCell($cell);                    
                    $cell->setParents($structure);                                     
                }                
            }            
        }
    }
    
    private function setResultsFromRequest(array $request) 
    {   
        if (count($request) !== 81) {
            throw new Exception('Request must contains 81 fields');
        }

        $similaritiesCount = 0;        
        foreach ($this->cells as $cell) {
            foreach ($request as $cellId => $value) {
                if ($cell->getId() == $cellId) {
                    $similaritiesCount++;
                }
            }
        }

        if ($similaritiesCount !== 81) {
            throw new Exception('Form\'s fields names must use this pattern "1-1-1" for (first square, first row, first column) ect ect'); 
        }
            
        foreach ($request as $cellId => $value) {
            foreach ($this->cells as $cell) {
                if ($cellId == $cell->getId()) {
                    if ($value !== "") {                        
                        $cell->setResultByRequest(intval($value));                                                                
                    }                    
                }
            }                
        }                   
    }

    private function solve()
    {
        foreach ($this->cells as $cell) {
            if ($cell->getResult() !== null) {
                $cell->updateParents();
            }
        }
    }
    
    private function isGridCompleted(array $cellArray) : bool
    {           
        foreach ($cellArray as $cell) {
            if ($cell->getResult() == null) {                             
                return false;
            }
        }
        return true;
    }

    private function cloneGrid() 
    {             
        $this->cloneCells();
        $this->cloneStructures();
        $this->clearParents();
        $this->clearChilds();
        $this->clearMissingNumbers();
        $this->mapping($this->cellsClones, $this->structuresClones);
        $this->UpdateClonedMissingNumbers();
    }

    public function getCells() : array
    {
        return $this->cells;
    }

    //Cloning Grid methods--------------------------------------------------------//
    //---------------------------------------------------------------------------//
    private function cloneCells()
    {
        foreach ($this->cells as $cell) {
            $cellClone = clone $cell;
            $this->cellsClones[$cellClone->getId()] = $cellClone;
        }
    }

    private function cloneStructures() 
    {
        foreach ($this->structures as $structure) {
            $structureClone = clone $structure;
            $this->structuresClones[$structureClone->getId()] = $structureClone;
        }
    }

    private function clearParents() 
    {
        foreach ($this->cellsClones as $cell) {
            $cell->clearParents();
        }
    }

    private function clearChilds()
    {
        foreach ($this->structuresClones as $structure) {
            $structure->clearCells();
        }
    }

    private function clearMissingNumbers() 
    {
        foreach ($this->structuresClones as $structure) {
            $structure->createMissingNumbers();
        }
    }

    private function UpdateClonedMissingNumbers()
    {
        foreach ($this->structuresClones as $structure) {
            $structure->updateMissingNumbers();
        }
    }
    //------------------------------------------------------------------------//
    //------------------------------------------------------------------------//

    private function findBestProbability() : array
    {
        $splObjectToSolve;
        $numberToTry;
        //Splboject Storage contain a maximum of 9 Cell object        
        $fullSplObjectCount = 9;

        foreach ($this->structures as $structure) {
            foreach ($structure->getMissingNumbers() as $missingNumber => $splObject) {
                if ($splObject->count() <= $fullSplObjectCount) {                    
                    $number = $missingNumber;                    
                    $splObjectToSolve = $splObject;
                }
            }
        }  
        
        $bestProbability[$number] = $splObjectToSolve;        
        return $bestProbability;
    }

    private function tryToSolve() : bool
    {           
        $bestProbability = $this->findBestProbability();        
        $number = array_key_first($bestProbability);
        $cellsToTry = [];
        
        foreach ($bestProbability as $splObject) {
            foreach ($splObject as $cell) {
                $cellsToTry[$cell->getId()] = true;
            }
        }
        
        for ($i=0; $i < count($bestProbability[$number]); $i++) {

            if ($i > 0) {
                //If the grid is not completed after the first iteration, we need to recreate a fresh copy.
                $this->cloneGrid();
            }
            
            foreach ($cellsToTry as $cellId => $bool) {
                if ($bool) {
                    $this->cellsClones[$cellId]->setResultByRequest($number);
                    $cellsToTry[$cellId] = false;
                    //Here we try to set the number in one of the cloned cell
                    //Then we set $cellsToTry[$cellId] to false to avoid doing the same operation
                    //in the next iteration of the FOR Loop 
                    break;
                }
            }

            $isGridCompleted = $this->isGridCompleted($this->cellsClones);
            //If the grid is completed we can return true    
            if ($isGridCompleted) {
                $this->cells = $this->cellsClones;
                $this->structures = $this->structuresClones;
                return true;                
            }
            //If it's not the For loop continues, it will recreate a new $this->cloneGrid() 
            //and then will try to set the number in a cell that is still true in 
            //$cellsToTry[$cellId]       
        } 

        return false; 
    } 

    public function getIsCompleted() : bool
    {
        return $this->isCompleted;
    }
}

