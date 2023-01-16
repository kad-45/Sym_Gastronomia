<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPasswordType;
use App\Form\UserType;
use App\Services\Helpers;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends AbstractController
{
    private LoggerInterface $logger;
    private EventDispatcherInterface $dispatcher;
    private Helpers $helpers;
    public function __construct(
        LoggerInterface $logger,
        EventDispatcherInterface $dispatcher,
        Helpers $helpers)
    {

    }

    #[Route('/user', name: 'app_users_list')]
    public function index(ManagerRegistry $doctrine): Response
    {
        //dd($this->getUser()->getRoles());
        if ($this->getUser()->getRoles()[0] != 'ROLE_ADMIN') {

            return $this->redirectToRoute('app_recipe_list');
        }

        $repository = $doctrine->getRepository(User::class);
        $users = $repository->findAll();


        return $this->render('user/users_list.html.twig', [
            'users' => $users
        ]);
    }
    /**
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/user/update/{id<\d+>?0}', name: 'app_user_update')]
    public function update(
        User                        $user,
        Request                     $request,
        EntityManagerInterface      $manager,
        UserPasswordHasherInterface $harsher): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->getUser() !== $user && $this->getUser()->getRoles()[0] != 'ROLE_ADMIN') {
            return $this->redirectToRoute('app_recipe_list');
        }
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($harsher->isPasswordValid($user, $form->getData()->getPlainPassword())) {

                $user = $form->getData();
                $manager->persist($user);
                $manager->flush();

                $this->addFlash('success', 'Votre compte a été modifié avec succés');

                return $this->redirectToRoute('app_recipe_list');
            } else {
                $this->addFlash('warning', 'Votre mot de passe est incorrect');
            }

        }

        return $this->render('user/update.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param User $user
     * @param Request $request
     * @param UserPasswordHasherInterface $hasher
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/user/edit_password/{id}', name: 'app_edit_password')]
    public function editPassword(User $user,
    Request $request,
    UserPasswordHasherInterface $hasher,
    EntityManagerInterface $manager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if ($this->getUser() !== $user) {
            return $this->redirectToRoute('app_recipe_list');
        }
        $form = $this->createForm(UserPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($hasher->isPasswordValid($user, $form->getData()['plainPassword'])) {

                $user = setUpdatedAt(new \DateTimeImmutable());
                $user->setPlainPassword($hasher->hashPassword($user, $form->getData()['newPlainPassword']));

                $manager->persist($user);
                $manager->flush();

                $this->addFlash('success', 'Votre mot de passe a été modifié avec succés');

                return $this->redirectToRoute('app_recipe_list');
            } else {
                $this->addFlash('warning', 'Votre mot de passe est incorrect');
            }
        }
        return $this->render('user/edit_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
