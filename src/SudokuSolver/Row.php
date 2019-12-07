<?php

namespace App\SudokuSolver;

use App\SudokuSolver\Structure;

class Row extends Structure{

    public function __construct(int $id){
        $this->id = 'R' . $id;        
    }   
   
}