<?php

namespace App\Controller;

use App\Repository\IngredientRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{
    #[Route('/ingredient', name: 'app_ingredient')]
    public function index(IngredientRepository $Repository, PaginatorInterface $paginator, Request $request): Response
    {
        // uncomment this and pass $ ingredients into the return
        // $ingredients = $Repository->findAll();

        $ingredients = $paginator->paginate(
            $Repository->findAll(), 
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
    
 
        return $this->render('pages/ingredient/index.html.twig', ['ingredients' => $ingredients]);
    }
}
