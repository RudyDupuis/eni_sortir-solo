<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController
{
    #[Route('/administration', name: 'admin_panel')]
    public function admin(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $findByMail = '';
        $findById = '';
        $userById = null;
        $userByEmail = null;

        $searchForm = $this->createFormBuilder()
            ->add('email', EmailType::class, ['required' => false])
            ->add('id', IntegerType::class, ['required' => false])
            ->getForm();

        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $data = $searchForm->getData();
            $findByMail = $data['email'];
            $findById = $data['id'];
        }

        if ($findByMail) {
            $userByEmail = $userRepository->findByEmail($findByMail);
        }
        if ($findById) {
            $userById = $userRepository->findById($findById);
        }

        $roleForm = $this->createFormBuilder()
            ->add('userId', IntegerType::class)
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'ROLE_USER' => '["ROLE_USER"]',
                    'ROLE_BANNED' => '["ROLE_BANNED"]',
                    'ROLE_ANIMATOR' => '["ROLE_ANIMATOR"]',
                    'ROLE_ADMIN' => '["ROLE_ADMIN"]',
                ],
                'label' => 'Liste des rôles',
            ])
            ->add('action', ChoiceType::class, [
                'choices' => [
                    'Ajouter' => 'ADD',
                    'Supprimer' => 'REMOVE',
                ],
                'label' => 'Action',
            ])
            ->getForm();

        $roleForm->handleRequest($request);
        $roleFormMessage = null;

        if ($roleForm->isSubmitted() && $roleForm->isValid()) {
            $formData = $roleForm->getData();
            $user = $userRepository->find($formData['userId']);

            if (!$user) {
                $roleFormMessage = 'L\'utilisateur n\'existe pas';
            } else {
                $action = $formData['action'];
                $roles = json_decode($formData['role'], true);

                if ($action === 'ADD') {
                    $currentRoles = $user->getRoles();
                    $newRoles = array_merge($currentRoles, $roles);
                    $user->setRoles(array_unique($newRoles));
                } elseif ($action === 'REMOVE') {
                    $currentRoles = $user->getRoles();
                    $newRoles = array_diff($currentRoles, $roles);
                    $user->setRoles(array_unique($newRoles));
                }

                $entityManager->persist($user);
                $entityManager->flush();
                $roleFormMessage = 'Les rôles ont bien été modifiées';
            }
        }

        return $this->render('pages/admin.html.twig', [
            'userByEmail' => $userByEmail,
            'userById' => $userById,
            'searchForm' => $searchForm->createView(),
            'roleForm' => $roleForm->createView(),
            'roleFormMessage' => $roleFormMessage,
        ]);
    }
}
