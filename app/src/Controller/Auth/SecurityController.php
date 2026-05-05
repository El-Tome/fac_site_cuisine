<?php

namespace App\Controller\Auth;

use App\Entity\User;
use App\Form\LoginFormType;
use App\Form\UserEditType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_user_profile');
        }

        $form = $this->createForm(LoginFormType::class, [
            '_email' => $authenticationUtils->getLastUsername(),
        ]);

        return $this->render('user/security/login.html.twig', [
            'form' => $form,
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void {}

    #[Route(path: '/user/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $originalEmail  = $user->getEmail();
        $originalPseudo = $user->getPseudo();

        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $currentPassword */
            $currentPassword = $form->get('currentPassword')->getData();

            if (!$userPasswordHasher->isPasswordValid($user, $currentPassword)) {
                $form->get('currentPassword')->addError(new FormError('Current password is incorrect'));
                return $this->render('user/edit.html.twig', [
                    'form' => $form,
                ]);
            }

            // Vérifier si l'email a changé et s'il existe déjà
            if ($user->getEmail() !== $originalEmail) {
                $existingUser = $userRepository->findOneBy(['email' => $user->getEmail()]);
                if ($existingUser && $existingUser->getId() !== $user->getId()) {
                    $form->get('email')->addError(new FormError('Email is already in use'));
                    return $this->render('user/edit.html.twig', [
                        'form' => $form,
                    ]);
                }
            }

            // Mettre à jour pseudo_id si le pseudo a changé
            if ($user->getPseudo() !== $originalPseudo) {
                $user->setPseudoId($userRepository->getLastPseudoIdFor($user->getPseudo()) + 1);
            }

            // Hasher le nouveau mot de passe s'il est fourni
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Profile updated successfully!');
            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/user/delete', name: 'app_user_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('GET')) {
            return $this->render('user/delete.html.twig');
        }

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('delete_account', $request->request->get('_token'))) {
                $this->addFlash('error', 'Invalid security token');
                return $this->render('user/delete.html.twig');
            }

            $confirmed = $request->request->get('confirm_delete');
            if (!$confirmed) {
                $this->addFlash('error', 'You must confirm account deletion');
                return $this->render('user/delete.html.twig');
            }

            $entityManager->remove($user);
            $entityManager->flush();

            $tokenStorage->setToken(null);
            $request->getSession()->invalidate();

            return $this->redirectToRoute('app_home_page');
        }

        return $this->redirectToRoute('app_user_profile');
    }
}
