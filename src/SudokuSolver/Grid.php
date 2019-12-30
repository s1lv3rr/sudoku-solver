<?php

namespace App\SudokuSolver;

use App\SudokuSolver\Cell;
use App\SudokuSolver\Structure;

//Initialize and manage all squares, rows and columns along with the cells.
class Grid 
{   
    //Array of Structure object, structure are the squares, rows and columns.
    //In the Cell class, they are refered as "parents", Cells are the Structures's childs     
    private $structures = [];
    private $cells = [];   

    public function __construct(array $request) 
    {        
        $this->createStructures();
        $this->createCells();
        $this->cellsMapping();
        $this->setResultsFromRequest($request);              
    }
    //Creates 27 parents objects (9 squares, 9 rows and 9 columns) and store them in this object "structures" property    
    private function createStructures() : void 
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
    private function createCells() {
        for ($square=1; $square < 10; $square++) { 
            if ($square < 4) {
                //Square 1 to 3 shares the same rows                 
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
    //Map the rights Cells in the right Structures
    //NB : $structure->setCell() also triggers $structure->attachChilds();  
    private function cellsMapping() {
        foreach ($this->cells as $cell) {
            $cellExplodedId = explode("-",$cell->getId());
            foreach ($this->structures as $structure) {
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
    //Sets the numbers (Result) sent by the user
    private function setResultsFromRequest(array $request) 
    {        
        foreach ($request as $cellId => $value) {
            foreach ($this->cells as $cell) {
                if ($cellId == $cell->getId()) {
                    if ($value !== "") {
                        $setResult = $cell->setResultByUser(intval($value));                                                                
                    }                    
                }
            }                
        }                   
    }    

    public function getCells() : array
    {
        return $this->cells;
    }
    
}

