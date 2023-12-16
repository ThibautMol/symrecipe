<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{
    /**
     * This controller display all ingredients
     * 
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', name: 'ingredient.index', methods:['GET'])]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        // uncomment this and pass $ ingredients into the return
        // $ingredients = $Repository->findAll();

        $ingredients = $paginator->paginate(
            $repository->findAll(), 
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
    
 
        return $this->render('pages/ingredient/index.html.twig', ['ingredients' => $ingredients]);
    }

    /**
     * This controller show a form which create an ingredient 
     * 
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient/nouveau', name: 'ingredient.new', methods:['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $ingredient = new Ingredient ();

        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $ingredient= $form->getData();

            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash('success','Votre ingrédient a été créé avec succès !');

            return $this->redirectToRoute('ingredient.index');

        }

        return $this->render('pages/ingredient/new.html.twig', ['form' => $form->createView()]);
    }


    /**
     * This controller show a form which edit an ingredient 
     * 
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @param Ingredient $ingredient
     * @return Response
     */
    #[Route('/ingredient/edition/{id}', name:'ingredient.edit', methods: ['GET', 'POST'])]
    public function edit(Ingredient $ingredient, Request $request, EntityManagerInterface $manager): Response
    {

        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $ingredient= $form->getData();

            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash('success','Votre ingrédient a été modifié avec succès !');

            return $this->redirectToRoute('ingredient.index');

        }
        
        return $this->render('pages/ingredient/edit.html.twig', ['form' => $form->createView()]);
    }


    /**
     * This controller show a form which delete an ingredient 
     * 
     * @param EntityManagerInterface $manager
     * @param Ingredient $ingredient
     * @return Response
     */
    #[Route('/ingredient/suppression/{id}', name:'ingredient.delete', methods: ['GET'])]
    public function delete(Ingredient $ingredient, EntityManagerInterface $manager) : Response
    {
    
        if(!$ingredient)
        {
            $this->addFlash('warning','L\'ingrédient n\'a pas été trouvé');

            return $this->redirectToRoute('ingredient.index');
        }

        $manager->remove($ingredient);
        $manager->flush();

        $this->addFlash('success','Votre ingrédient a été supprimé avec succès !');

        return $this->redirectToRoute('ingredient.index');
    }
}
