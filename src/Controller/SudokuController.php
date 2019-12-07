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
            
        }      


        return $this->render('sudoku/index.html.twig', [
            'controller_name' => 'SudokuController',
        ]);
    }

    /**
     * @Route("/solver", name="solver", methods={"POST"})
     */
    public function solver(Request $request)
    {
        dd($request);
        return $this->render('sudoku/index.html.twig', [
            'controller_name' => 'SudokuController',
        ]);
    }
}
