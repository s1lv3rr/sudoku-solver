<?php

namespace App\SudokuSolver;

use App\SudokuSolver\Row;
use App\SudokuSolver\Cell;
use App\SudokuSolver\Column;
use App\SudokuSolver\Square;
use App\SudokuSolver\RowProbability;
use App\SudokuSolver\ColumnProbability;
use App\SudokuSolver\SquareProbability;

//The class that manages all square, row and colmun along with the cells within
class Grid {

    private $squares = [];
    private $rows = [];
    private $columns = [];
    private $cells = [];
        

    public function __construct(array $request) {
        $this->createStructure();
        $this->initialise($request);
        $this->calculateBestProbabilities();
        $this->resolve();                     
        dump($this);                    
    }

    //Create 9 square objects, 9 row objects and 9 column objects and store them in this object properties
    private function createStructure() : void {
        for ($i=1; $i <10; $i++) {
            $square = new Square($i); //The object id is sent to the construct           
            $row = new Row($i);            
            $column = new Column($i);             
            array_push($this->squares, $square);
            array_push($this->rows, $row);
            array_push($this->columns, $column);
        }
    }

    //Initialise the grid by creating the 81 cells of the game and map them into the right square, row and column
    //The request array MUST contain 81 lines in this format ["1-1-1" => "numberSentByTheUser"]
    //Each cell will be identified by these keys in the request array. "1-1-1" means first square, first row, first column
    //"3-2-9" : Third square, second line, 9th column. and so on..
    private function initialise(array $request) : void {

        foreach ($request as $cellId => $value) {
            $cell = new Cell($cellId);
            array_push($this->cells, $cell);            
            $explodedKey = explode("-",$cellId);    
            //Object Mapping
            foreach ($this->squares as $square) {
                if($square->getId() == $explodedKey[0]){
                    $square->setCell($cell);
                    $cell->setSquare($square);                   
                }
            }
            //Object Mapping
            foreach ($this->rows as $row) {
                if($row->getId() == $explodedKey[1]){
                    $row->setCell($cell);
                    $cell->setRow($row);                    
                }
            }
            //Object Mapping
            foreach ($this->columns as $column) {
                if($column->getId() == $explodedKey[2]){
                    $column->setCell($cell);
                    $cell->setColumn($column);                    
                }
            }        
        }
        
        $this->cellNumberSetter($request);
    }

    //Loop that set the numbers sent in the request after integrity check
    private function cellNumberSetter(array $request) : void {
        
        foreach ($request as $cellId => $value) {

            foreach ($this->cells as $cell) {

                if($cellId == $cell->getId()) {

                    if($value !== "" && $this->integrityCheck($cell, $value)) {                        
                        $cell->setNumber(intval($value)); //setNumber also updates the probabilties in his object, see Cell class.
                        $this->siblingsUpdate($cell, $value);                        
                    }
                }
            }                
        }           
    }

    //Check that a number is not duplicated in a row, column or square
    private function integrityCheck(Cell $cell, int $number) : bool {
        
        $squareTocheck = $cell->getSquare();
        $rowToCheck = $cell->getRow();
        $columnToCheck = $cell->getColumn();
        
        foreach($squareTocheck->getCells() as $currentCell) {
            if($currentCell->getNumber() == $number && $currentCell !== $cell) {
                return false;
            }
        }

        foreach($rowToCheck->getCells() as $currentCell) {
            if($currentCell->getNumber() == $number && $currentCell !== $cell) {
                return false;
            }
        }
        
        foreach($columnToCheck->getCells() as $currentCell) {
            if($currentCell->getNumber() == $number && $currentCell !== $cell) {
                return false;
            }
        }        
        //Return true if everything is ok
        return true;
    }

    //When a number is updated in a cell, this function updates the probabilities in the related cells.
    //For example if you put a 1 in a cell, all the others cells from the same square, row and columnn can not have this number anymore
    private function siblingsUpdate(Cell $cell, int $number) : void {

        $square = $cell->getSquare();
        $row = $cell->getRow();
        $column = $cell->getColumn();

        foreach ($square->getCells() as $currentCell) {
            if($cell !== $currentCell) {
                foreach ($cell->getProbabilities() as $key => $value) {
                    if($key == $number) {
                        $currentCell->updateProbabilities($number);
                    }
                }
            }            
        }
        foreach ($row->getCells() as $currentCell) {
            if($cell !== $currentCell) {
                foreach ($cell->getProbabilities() as $key => $value) {
                    if($key == $number) {
                        $currentCell->updateProbabilities($number);
                    }
                }
            }            
        }
        foreach ($column->getCells() as $currentCell) {
            if($cell !== $currentCell) {
                foreach ($cell->getProbabilities() as $key => $value) {
                    if($key == $number) {
                        $currentCell->updateProbabilities($number);
                    }
                }
            }            
        }
    }

    private function calculateBestProbabilities(){
        foreach ($this->squares as $square) {
            $square->calculateBestProbabilities();
        }
        foreach ($this->rows as $row) {
            $row->calculateBestProbabilities();
        }
        foreach ($this->columns as $column) {
            $column->calculateBestProbabilities();
        }
    }

    private function Resolve() {
        foreach ($this->squares as $square) {
            foreach($square->getBestProbabilities() as $number => $array) {
                if(isset($array[1])) {                    
                    $cell = $array[1][0];
                    $cell->setNumber(intval($number)); 
                    $this->siblingsUpdate($cell, $number);                        
                }
            }
        }
        foreach ($this->rows as $row) {
            foreach($row->getBestProbabilities() as $number => $array) {
                if(isset($array[1])) {                    
                    $cell = $array[1][0];
                    $cell->setNumber(intval($number)); 
                    $this->siblingsUpdate($cell, $number);                        
                }
            }
        }
        foreach ($this->columns as $column) {
            foreach($column->getBestProbabilities() as $number => $array) {
                if(isset($array[1])) {                    
                    $cell = $array[1][0];
                    $cell->setNumber(intval($number)); 
                    $this->siblingsUpdate($cell, $number);                        
                }
            }
        }
    }
   
    
}