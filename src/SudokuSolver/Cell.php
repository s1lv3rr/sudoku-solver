<?php

namespace App\SudokuSolver;

class Cell {
    
    private $id;
    private $result = null;
    private $resultPossibilities = [
        1 => true,
        2 => true,
        3 => true,
        4 => true,
        5 => true,
        6 => true,
        7 => true,
        8 => true,
        9 => true,
    ];
    private $square;
    private $row;
    private $column;
    
    public function __construct(string $id){
        $this->id = $id;
    }
    //When the result has been found, all the others numbers have to be set to false
    public function setResult(int $number) : bool {

        if ($this->result == null && $this->integrityCheck($number)) {

            $this->result = $number;
        
            foreach ($this->resultPossibilities as $key => $value) {
                if ($key !== $number) {
                    $this->resultPossibilities[$key] = false;
                }
            }

            $this->square->updateSiblingsResultPossibilities($this, $this->result);
            $this->row->updateSiblingsResultPossibilities($this, $this->result);
            $this->column->updateSiblingsResultPossibilities($this, $this->result);
            $this->square->unsetProbability($this);
            $this->row->unsetProbability($this);
            $this->column->unsetProbability($this);   
            $this->square->calculateProbabilities();
            $this->row->calculateProbabilities();
            $this->column->calculateProbabilities();                                              
            
            return true;
        }

        return false;
        
    }

    private function integrityCheck(int $number) : bool {

        $numberOfOccurences = 0;        
        foreach ($this->square->getCells() as $cell) {
            if($cell !== $this && $cell->getResult() == $number) {
                $numberOfOccurences++;
            }
        }
        foreach ($this->row->getCells() as $cell) {
            if($cell !== $this && $cell->getResult() == $number) {
                $numberOfOccurences++;
            }
        }
        foreach ($this->column->getCells() as $cell) {
            if($cell !== $this && $cell->getResult() == $number) {
                $numberOfOccurences++;
            }
        }
        
        if($numberOfOccurences > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function updateResultPossibilities(int $number) : void
    {
        $this->resultPossibilities[$number] = false;
        //Auto update itselfs if there is only one number possible in this cell regardless of his parent
        $boolIteration = 0;
        $number;

        foreach ($this->resultPossibilities as $key => $value) {
            if($value) {
                $boolIteration++;
                $number = $key;
            }
        }
        if($boolIteration == 1) {
            $this->setResult($number);
        }
    }
    
    public function getResult() : ?int {

        return $this->result;        
        
    }
    
    public function getSquare() : ?Square
    {
        return $this->square;
    }

    public function setSquare(Square $square)
    {
        $this->square = $square;

        return $this;
    }
    
    public function getRow() : ?Row
    {
        return $this->row;
    }
    
    public function setRow(Row $row)
    {
        $this->row = $row;

        return $this;
    }
    
    public function getColumn() : ?Column
    {
        return $this->column;
    }
   
    public function setColumn(Column $column)
    {
        $this->column = $column;

        return $this;
    }

    public function getId() : string
    {
        return $this->id;
    }
    
    public function getResultPossibilities() : array
    {
        return $this->resultPossibilities;
    }   
       
}