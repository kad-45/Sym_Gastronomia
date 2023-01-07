<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class RecipeController extends AbstractController
{
    /**
     * @param RecipeRepository $recipeRepo
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/recipe', name: 'app_recipe_list')]
    public function index(RecipeRepository $recipeRepo,
    PaginatorInterface $paginator,
    Request $request): Response
    {
        $recipes = $paginator->paginate($recipeRepo->findAll(),
        $request->query->getInt('page', 1),
            10);
        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }

    /**
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @param SluggerInterface $slugger
     * @return Response
     */
    #[Route('/recipe/new', name: 'app_new_recipe')]
    public function new(
    EntityManagerInterface $manager,
    Request $request,
    SluggerInterface $slugger): Response
    {
        $recipe = new Recipe();
        $form =$this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $imageFile */

            $imageFile = $form->get('image')->getData();

            // this condition is needed because the 'image' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $recipe->setImageFilename($newFilename);
            }

            // ... persist the $recipe variable or any other work

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette : ' . $recipe->getName() . ' a été ajouté avec succeés!'
            );

            return $this->redirectToRoute('app_recipe_list');
        }

        return $this->render('recipe/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/recipe/update/{id}', name: 'app_update_recipe')]
    public function update(Recipe $recipe, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();

            $manager->persist($recipe);

            $manager->flush();

            $this->addFlash(
                'success',
                'Votre ingrédient : ' . $recipe->getName() . ' a été mis à jour avec succeés!'
            );

            return $this->redirectToRoute('app_recipe_list');
        }
        return $this->render('recipe/update.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/recipe/delete/{id}', name: 'app_delete_recipe')]
    public function delete(EntityManagerInterface $manager,
                           Recipe $recipe): Response
    {

        $manager->remove($recipe);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre recette : ' . $recipe->getName() . ' a été supprimé avec succeés!'
        );
        return $this->redirectToRoute('app_recipe_list');
    }
}
