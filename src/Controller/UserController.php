<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\ProfileFormType;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class UserController extends AbstractController
{
    #[Route('/inscription', name: 'user_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('outing_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('pages/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/connexion', name: 'user_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('outing_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('pages/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/deconnexion', name: 'user_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/profile', name: 'user_profile')]
    public function profile(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('plainPassword')->getData();
            $profilePictureFile = $form->get('profilePicture')->getData();

            if ($profilePictureFile instanceof UploadedFile) {
                $oldProfilePicture = $user->getProfilePicture();

                if ($oldProfilePicture) {
                    $oldProfilePicturePath = $this->getParameter('profile_pictures_directory') . '/' . $oldProfilePicture;
                    if (file_exists($oldProfilePicturePath)) {
                        unlink($oldProfilePicturePath);
                    }
                }

                $newFileName = md5(uniqid()) . '.' . $profilePictureFile->guessExtension();

                $profilePictureFile->move(
                    $this->getParameter('profile_pictures_directory'),
                    $newFileName
                );

                $user->setProfilePicture($newFileName);
            }

            if ($newPassword) {
                $hashedPassword = $userPasswordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            }

            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->render('pages/profile.html.twig', [
            'profileForm' => $form->createView(),
            'user' => $user,
        ]);
    }
}
