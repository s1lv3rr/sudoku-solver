<?php

namespace App\SudokuSolver;

use App\SudokuSolver\Structure;

class Square extends Structure{    

    public function __construct(int $id){
        $this->id = 'S' . $id;             
    }   
    
}