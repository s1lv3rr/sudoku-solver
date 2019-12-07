<?php

namespace App\SudokuSolver;

use App\SudokuSolver\Row;
use App\SudokuSolver\Cell;
use App\SudokuSolver\Column;
use App\SudokuSolver\Square;
use App\SudokuSolver\Probability;


//The class that manages all square, row and colmun along with the cells within
class Grid {

    private $squares = [];
    private $rows = [];
    private $columns = [];
    private $cells = [];
    private $probabilities = [];  
        

    public function __construct(array $request) {
        $this->create();
        $this->initialise($request);        
        $this->getAllProbabilities();
        $this->resolve();     
        dump($this);        
    }

    //Create 9 square objects, 9 row objects and 9 column objects and store them in this object properties
    //These 3 classes are childs of Structure class
    private function create() : void {
        for ($i=1; $i <10; $i++) {
            $square = new Square($i); //The object id is sent to the construct           
            $row = new Row($i);            
            $column = new Column($i);             
            $this->squares += [$square->getId() => $square];
            $this->rows += [$row->getId() => $row];
            $this->columns += [$column->getId() => $column];
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
                if($square->getId() == "S".$explodedKey[0]){
                    $square->setCell($cell);
                    $cell->setSquare($square);                   
                }
            }
            //Object Mapping
            foreach ($this->rows as $row) {
                if($row->getId() == "R".$explodedKey[1]){
                    $row->setCell($cell);
                    $cell->setRow($row);                    
                }
            }
            //Object Mapping
            foreach ($this->columns as $column) {
                if($column->getId() == "C".$explodedKey[2]){
                    $column->setCell($cell);
                    $cell->setColumn($column);                    
                }
            }        
        }
        $this->createProbabilities();
        $this->massCellResultSetter($request);        
    }

    //Loop that set the result (confirmed number for a cell) sent in the request after integrity check
    private function massCellResultSetter(array $request) : void {
        
        foreach ($request as $cellId => $value) {

            foreach ($this->cells as $cell) {

                if($cellId == $cell->getId()) {

                    if($value !== "") {                        
                        $this->numberSetter($cell, $value);//setResult also updates the probabilties in his object, and check the integrity of the number see Cell class.                                               
                    }
                    if($value == ""){
                        $this->calculateProbabilities();
                    }
                }
            }                
        }           
    }

    private function numberSetter($cell, $number) {
        $cell->setResult(intval($number));
        $this->calculateProbabilities();
    }

    private function calculateProbabilities() {
        foreach ($this->squares as $square) {
            $square->calculateProbabilities();
        }
        foreach ($this->rows as $row) {
            $row->calculateProbabilities();
        }
        foreach ($this->columns as $columns) {
            $columns->calculateProbabilities();
        }
    }
        
    private function createProbabilities() {
        foreach ($this->squares as $square) {
            $square->createProbabilities();
        }
        foreach ($this->rows as $row) {
            $row->createProbabilities();
        }
        foreach ($this->columns as $column) {
            $column->createProbabilities();
        }
    }

    //Stores all the Probability objects in this class probabilities array.
    private function getAllProbabilities() : void {
        foreach ($this->squares as $square) {
            foreach ($square->getProbabilities() as $probability) {
                array_push($this->probabilities, $probability);
            }
        }
        foreach ($this->rows as $row) {
            foreach ($row->getProbabilities() as $probability) {
                array_push($this->probabilities, $probability);
            }
        }
        foreach ($this->columns as $column) {
            foreach ($column->getProbabilities() as $probability) {
                array_push($this->probabilities, $probability);
            }
        }
    }
       
    private function resolve() {

        for ($i=1; $i<10; $i++) {
           
            foreach ($this->probabilities as $probability) {
                if (count($probability->getCells()) == 1) {
                    $keyOfFirstElement = array_key_first($probability->getCells());                    
                    $cell = $probability->getCells()[$keyOfFirstElement];
                    $number = $probability->getNumber();                    
                    $this->numberSetter($cell,$number); 
                }
            }
        }            
    }

    private function isPuzzleOver() {

        $count = 0;

        foreach ($this->probabilities as $probability) {
            if(count($probability->getCells()) == 1) {
                $count++;
            }
        }
        if($count > 0) {
            return true;
        } else {
            return false;
        }
    }
}

