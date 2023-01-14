<?php

namespace App\Controller;


use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\IngredientRepository;
use App\Repository\RecipeRepository;
use App\Services\UploaderServices;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



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
    IngredientRepository $ingredientRepo,
    PaginatorInterface $paginator,
    Request $request): Response
    {

        $recipes = $paginator->paginate($recipeRepo->findAll(),
        $request->query->getInt('page', 1),
            10);
        $ingredients = $ingredientRepo->findAll();
        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes,
            'ingredients' => $ingredients
        ]);
    }

    /**
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @param UploaderServices $uploaderServices
     * @return Response
     */
    #[Route('/recipe/new', name: 'app_new_recipe')]
    public function new(
    EntityManagerInterface $manager,
    Request $request,
    UploaderServices $uploaderServices): Response
    {
        $recipe = new Recipe();
        $form =$this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $imageFile */

            $imageFile = $form->get('imageFilename')->getData();

            // this condition is needed because the 'image' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($imageFile) {

                $directory = $this->getParameter('images_directory');
                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $recipe->setImageFilename($uploaderServices->uploadFile($imageFile, $directory));
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

    /**
     * @param Recipe $recipe
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UploaderServices $uploaderServices
     * @return Response
     */
    #[Route('/recipe/update/{id}', name: 'app_update_recipe')]
    public function update(Recipe $recipe,
    Request $request,
    EntityManagerInterface $manager,
     UploaderServices $uploaderServices): Response
    {

        $imageFilename = $recipe->getImageFilename();
        //on hydrate le champ du form avec le nom du fichier image
        if(!is_null($imageFilename)){

            $recipe->setImageFilename($imageFilename);
        }
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        $recipe = $form->getData();

        $imageFile = $form['imageFilename']->getData();
        $directory = $this->getParameter('images_directory');


        if ($form->isSubmitted() && $form->isValid()) {
            if ( $recipe->getImageFilename() !== null){
                $recipe->setImageFilename(
                    $uploaderServices->uploadFile($imageFile, $directory));
            }

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette : ' . $recipe->getName() . ' a été mis à jour avec succeés!'
            );

            return $this->redirectToRoute('app_recipe_list');
        }
        return $this->render('recipe/update.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param EntityManagerInterface $manager
     * @param Recipe $recipe
     * @return Response
     */
    #[Route('/recipe/delete/{id}', name: 'app_delete_recipe')]
    public function delete(
    EntityManagerInterface $manager,
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
