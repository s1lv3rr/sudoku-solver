<?php
namespace App\SudokuSolver;
//Mother class of Sqaure, row and column
class Structure {

    protected $id;    
    protected $cells = [];    
    protected $probabilities = []; 
       
    public function createProbabilities() {
        for ($i=1; $i < 10; $i++) {
            $probability = new Probability($i, $this);
            $this->probabilities += [$i => $probability];                                 
        } 
        
        $this->calculateProbabilities();
    }       

    public function updateSiblingsResultPossibilities(Cell $cell, int $result) {
        foreach ($this->cells as $currentCell) {
            if($currentCell !== $cell) {
                $currentCell->updateResultPossibilities($result);
            }
        }        
    }

    public function calculateProbabilities() {

        foreach ($this->cells as $cell) {
            foreach ($cell->getResultPossibilities() as $number => $value) {
                foreach ($this->probabilities as $probability) {
                    if($probability->getNumber() == $number) {
                        if(!$value) {
                            $probability->unsetCell($cell);
                        }
                        if($value && !in_array($cell, $probability->getCells())){                            
                            if($cell->getResult() == null) {
                                $probability->setCell($cell);                            
                            }
                        }                       
                    }
                }
            }
        }
    }

    public function unsetProbability(Cell $cell) {
        unset($this->probabilities[$cell->getResult()]);        
    }
 
    public function setCell(Cell $cell) {
        $this->cells += [$cell->getId() => $cell];
    }

    public function getCells() : array {
        return $this->cells;
    }     
   
    public function getId() : ?string
    {
        return $this->id;
    }
        
    public function getProbabilities() : array
    {
        return $this->probabilities;
    }
}