<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Form\AccountType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/account')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_account')]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('front/user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/edit', name:'app_account_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();
        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('oldPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();

            if(($oldPassword != null && $newPassword != null) || ($oldPassword != "" && $newPassword != "")){
                if ($passwordHasher->isPasswordValid($user, $form->get('oldPassword')->getData())) {
                    $newEncodedPassword = $passwordHasher->hashPassword($user, $form->get('newPassword')->getData());
                    $user->setPassword($newEncodedPassword);
                    $manager->flush();
                    $this->addFlash('success', 'Mot de passe mis à jour avec succès');
                } else {
                    $this->addFlash('error', 'Ancien mot de passe incorrect.');
                }
            }

            $manager->flush();
            $this->addFlash('notice', 'Votre compte a été mis-à-jour !');

        }

        return $this->render('front/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
