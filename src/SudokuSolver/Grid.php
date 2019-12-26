<?php

namespace App\SudokuSolver;

use App\SudokuSolver\Row;
use App\SudokuSolver\Structure;

//The class that manages all square, row and colmun along with the cells within
class Grid 
{        
    private $structures = [];
    private $cells = [];   

    public function __construct(array $request) 
    {        
        $this->createParents();
        $this->createChilds($request);
        $this->initialiseStructuresMissingNumbers(); 
        $this->setResultsFromRequest($request); 
        dump($this);        
    }

    //Creates 27 parents objects (9 squares, 9 rows and 9 columns) and store them in this object "structures" property    
    private function createParents() : void 
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

    //Created the 81 cells of the game and map them into the right square, row and column
    //The request array MUST contain 81 lines in this format ["1-1-1" => "numberSentByTheUser"]
    //Each cell will be identified by these keys in the request array. "1-1-1" means first square, first row, first column
    //"3-2-9" : Third square, second line, 9th column. and so on..
    private function createChilds(array $request) : void 
    {
        foreach ($request as $cellId => $value) {            
            $cell = new Cell($cellId);            
            array_push($this->cells, $cell);            
            $cellExplodedId = explode("-",$cellId);    
            //Object Mapping
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

    private function initialiseStructuresMissingNumbers()
    {
        foreach ($this->structures as $structure) {
            $structure->initialiseMissingNumbers();
        }
    }

    //Sets the numbers (Result) sent by the request
    private function setResultsFromRequest(array $request) : void 
    {        
        foreach ($request as $cellId => $value) {
            foreach ($this->cells as $cell) {
                if ($cellId == $cell->getId()) {
                    if ($value !== "") {                        
                        $cell->setResult(intval($value));//setResult also updates the probabilties in his object, and check the integrity of the number see Cell class.                                               
                    }                    
                }
            }                
        }           
    }    
}

