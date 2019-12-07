<?php

namespace App\SudokuSolver;

class Row {
    
    private $id;
    private $cells = [];
    private $bestProbabilities = [];

    public function __construct(int $id){
        $this->id = $id;
    }

    public function calculateBestProbabilities() {

        foreach ($this->cells as $cell) {

            foreach ($cell->getProbabilities() as $key => $value) {

                if($value && $cell->getNumber() == null) {

                    if(!isset($this->bestProbabilities[$key])) {
                        $this->bestProbabilities[$key][1] = [$cell];                                              
                    } else {                        
                        $oldArrayKey = key($this->bestProbabilities[$key]);                                     
                        $intermediateArray = $this->bestProbabilities[$key][$oldArrayKey];
                        $oldArrayKey++; 
                        $this->bestProbabilities[$key][$oldArrayKey] = $intermediateArray;
                        array_push($this->bestProbabilities[$key][$oldArrayKey], $cell);
                        unset($this->bestProbabilities[$key][1]);
                    }
                }
            }
        }
    }

    public function setCell(Cell $cell) {
        array_push($this->cells, $cell);
    }

    public function getCells() : array {
        return $this->cells;
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function getBestProbabilities() : array
    {
        return $this->bestProbabilities;
    }
}