<?php

namespace App\Controller;


use App\SudokuSolver\Grid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SudokuController extends AbstractController
{
    /**
     * @Route("/", name="sudoku")
     */
    public function index(Request $request)
    {   
        if($request->isMethod('POST')) {

            $request = $request->request->all();            
            $grid = new Grid($request);            
            
            return $this->render('sudoku/result.html.twig', [
                'controller_name' => 'SudokuController', 
                'grid' => $grid           
            ]);
        }         
           
        return $this->render('sudoku/grid.html.twig', [
            'controller_name' => 'SudokuController',                      
        ]);
    }
    
}
