<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class IngredientController extends AbstractController
{
    /**
     * This function display all ingredients
     * @param IngredientRepository $ingredientRepo
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', name: 'app_ingredient')]
    public function index(
        IngredientRepository $ingredientRepo,
        PaginatorInterface $paginator,
        Request $request
    ): Response
    {
        $ingredients = $paginator->paginate(
            $ingredientRepo->findAll(),
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('ingredient/index.html.twig', [
            'ingredients' =>$ingredients
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */

    #[Route('/new', name: 'app_new')]
    public function new(Request $request,
    EntityManagerInterface $manager): Response
    {
        $ingredient = new Ingredient();

        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();

            $manager->persist($ingredient);

            $this->addFlash(
                'success',
                'Votre ingrédient : ' . $ingredient->getName() . ' a été ajouté avec succeés!'
            );

            $manager->flush();

            return $this->redirectToRoute('app_ingredient');
        }



        return $this->render('ingredient/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Ingredient $ingredient
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */

    #[Route('/update/{id}', name: 'app_update')]

    public function update(Ingredient $ingredient, Request $request, EntityManagerInterface $manager): Response
    {

        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();

            $manager->persist($ingredient);

            $manager->flush();

            $this->addFlash(
                'success',
                'Votre ingrédient : ' . $ingredient->getName() . ' a été mis à jour avec succeés!'
            );


            return $this->redirectToRoute('app_ingredient');
        }
        return $this->render('ingredient/update.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param EntityManagerInterface $manager
     * @param Ingredient $ingredient
     * @return Response
     */

    #[Route('/delete/{id}', name: 'app_delete')]
    public function delete(EntityManagerInterface $manager,
    Ingredient $ingredient): Response
    {

        $manager->remove($ingredient);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre ingrédient : ' . $ingredient->getName() . ' a été supprimé avec succeés!'
        );
        return $this->redirectToRoute('app_ingredient');
    }
}
