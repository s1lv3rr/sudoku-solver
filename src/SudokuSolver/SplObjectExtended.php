<?php

namespace App\SudokuSolver;
use SplObjectStorage;

//Not really used yet
class SplObjectExtended extends SplObjectStorage {

    public function getById($id) {  
        
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