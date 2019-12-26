<?php

namespace App\SudokuSolver;
use SplObjectStorage;

class SplObjectExtended extends SplObjectStorage {

    public function getObjectById($id) {  
        
        $this->rewind();

        foreach ($this as $object) {

            $info = $this->getInfo();

            if ($id === $info) {                
                return $object;
            }
        }
        return null;
    }
}