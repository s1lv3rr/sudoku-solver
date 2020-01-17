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
    private $isCompleted = false;
    private $incorrectGrid = false; 
    private $startTime;     

    public function __construct(array $request) 
    {        
        $this->createStructures();
        $this->createCells();
        $this->mapping($this->cells, $this->structures);
        $this->setResultsFromRequest($request);
        //Once all result sent from the user are setted (black colour)
        //We can start to solve the grid. Result found will then be displayed in green
        if ($this->isGridCorrect()) {
            $this->solve();
        }

        if(!$this->isCompleted && $this->isGridCorrect()) {
            $this->startTime = time();
            $this->backTrackTechnique();            
        } 

        $this->isCompleted = true;            
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
                        $cell->setResultByRequest($value);                                                                
                    }                    
                }
            }                
        }                   
    }
    //Triggers the update of each parents, resulting of a circular update between each parents and cells
    //Everytime a parent is updated, it will try to find a result using several solving methods  
    private function solve()
    {
        foreach ($this->cells as $cell) {
            if ($cell->getResult() !== null) {
                $cell->updateParents();
            }
        }
    }    
    //BackTracking method from https://www.youtube.com/watch?v=eqUwSA0xI-s
    //It ensures that the grid will be completed even if it's empty
    //I had to set back the cell's result property to public to make it work without modifying too much my code
    //Not ideal but working
    private function backTrackTechnique() 
    {   
        //Because this loop would turn for ever in case of a grid that is impossible to solve
        //I give it a 0.3sec timeout, after this time if the grid is not complete, it will be considered impossible 
        $currentTime = time() - $this->startTime;     
        if ($currentTime > 0.3) {
            $this->incorrectGrid = true;
            return;
        }

        $cellWithNoResult = $this->findCellWithNoResult();

        if ($cellWithNoResult == null) {

            return true;

        } else {

            for ($i=1;$i<10;$i++) {
                
                if (is_null($cellWithNoResult->duplicationCheck($i))) {
                    
                    $cellWithNoResult->result = $i;

                    if ($this->backTrackTechnique()) {
                        return true;
                    }

                    $cellWithNoResult->result = null;
                }
                
            }            
            return false;
        }
        $this->isCompleted = true;
    }

    private function findCellWithNoResult() : ?Cell
    {
        foreach ($this->cells as $cell) {
            if ($cell->getResult() == null) {
                return $cell;
            }
        }
        return null;
    } 
    
    private function isGridCorrect() : bool
    {
        foreach ($this->cells as $cell) {
            if ($cell->getDuplicatedNumber() !== null) {
                return false;
            }
        }
        return true;
    }

    public function getCells() : array
    {
        return $this->cells;
    } 
   
    public function getIncorrectGrid() : bool
    {
        return $this->incorrectGrid;
    }
}

