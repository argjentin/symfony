<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\EditProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserController extends AbstractController {
    #[Route('/change-password', name: 'edit_password')]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw new AccessDeniedException('You must be logged in to change your password.');
        }
    
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('oldPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();
    
            if (!$passwordHasher->isPasswordValid($user, $oldPassword)) {
                $this->addFlash('error', 'Ancien mot de passe incorrect.');
            } else {
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $newPassword
                    )
                );
    
                $entityManager->flush();
    
                $this->addFlash('success', 'Mot de passe mis à jour avec succès.');
    
                return $this->redirectToRoute('profil'); 
            }
        } else if ($form->isSubmitted() && !$form->isValid()) {
            // Récupération des erreurs de validation
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[$error->getOrigin()->getName()] = $error->getMessage();
            }
            // Envoyer les erreurs au template
            $this->addFlash('form_errors', $errors);
        }
    
        return $this->render('user/change_password.html.twig', [
            'changePasswordForm' => $form->createView(),
        ]);
    }

    #[Route('/edit-profil', name: 'edit_profil')]
    public function editProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw new AccessDeniedException('Vous devez être connecté pour modifier votre profil.');
        }

        $form = $this->createForm(EditProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();
            }
            catch (\Exception $e){
                return $this->render('user/edit_profil.html.twig', [
                    'editProfileForm' => $form->createView()
                ]);
            }

            $this->addFlash('success', 'Profil mis à jour avec succès.');

            return $this->redirectToRoute('profil');
        } else if ($form->isSubmitted() && !$form->isValid()) {
            // Récupération des erreurs de validation
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[$error->getOrigin()->getName()] = $error->getMessage();
            }
            // Envoyer les erreurs au template
            $this->addFlash('form_errors', $errors);
        }

        return $this->render('user/edit_profil.html.twig', [
            'editProfileForm' => $form->createView(),
        ]);
    }
}