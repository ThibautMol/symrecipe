<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class RecipeController extends AbstractController
{
    /**
     * This controller display all recipes
     * 
     * @param RecipeRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/recette', name: 'recipe.index', methods: ['GET'])]
    public function index(RecipeRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {

        $recipes = $paginator->paginate(
            $repository->findBy(['user' => $this->getUser()]), 
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }


    /**
     * This controller show a form which create a recipe 
     * 
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/recette/nouveau', name: 'recette.new', methods:['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $recipe = new Recipe ();

        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $recipe= $form->getData();
            $recipe->setUser($this->getUser());

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash('success','Votre recette a été créé avec succès !');

            return $this->redirectToRoute('recipe.index');

        }

        return $this->render('pages/recipe/new.html.twig', ['form' => $form->createView()]);
    }


    /**
     * This controller show a form which edit a recipe 
     * 
     * @param Recipe $recipe
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    #[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]
    #[Route('/recette/edition/{id}', name:'recipe.edit', methods: ['GET', 'POST'])]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $manager): Response
    {

        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $ingredient= $form->getData();

            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash('success','Votre recette a été modifiée avec succès !');

            return $this->redirectToRoute('recipe.index');

        }
        
        return $this->render('pages/recipe/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * This controller show a form which delete a recipe 
     * 
     * @param EntityManagerInterface $manager
     * @param Recipe $recipe
     * @return Response
     */
    #[Route('/recette/suppression/{id}', name:'recipe.delete', methods: ['GET'])]
    public function delete(Recipe $recipe, EntityManagerInterface $manager) : Response
    {
    
        if(!$recipe)
        {
            $this->addFlash('warning','La recette n\'a pas été trouvée');

            return $this->redirectToRoute('recipe.index');
        }

        $manager->remove($recipe);
        $manager->flush();

        $this->addFlash('success','Votre recette a été supprimée avec succès !');

        return $this->redirectToRoute('recipe.index');
    }
}


