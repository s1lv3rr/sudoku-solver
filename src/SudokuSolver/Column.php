<?php

namespace App\SudokuSolver;

use App\SudokuSolver\Structure;

class Column extends Structure {    

    public function __construct(int $id){
        $this->id = 'C' . $id;        
    }   
    
}