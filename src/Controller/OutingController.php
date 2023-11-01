<?php

namespace App\Controller;

use App\Entity\Outing;
use App\Form\OutingCreateFormType;
use App\Form\OutingUpdateFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class OutingController extends AbstractController
{
    #[Route('/', name: 'outing_home')]
    public function index(): Response
    {
        return $this->render('pages/home.html.twig');
    }

    #[Route('/cree-une-sortie', name: 'outing_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $outing = new Outing();
        $form = $this->createForm(OutingCreateFormType::class, $outing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $outingImageFile = $form->get('outingImage')->getData();

            if ($outingImageFile instanceof UploadedFile) {
                $newFileName = md5(uniqid()) . '.' . $outingImageFile->guessExtension();

                $outingImageFile->move(
                    $this->getParameter('outing_pictures_directory'),
                    $newFileName
                );

                $outing->setOutingImage($newFileName);
            }

            $outing->setAuthor($this->getUser());

            $entityManager->persist($outing);
            $entityManager->flush();

            return $this->redirectToRoute('outing_update', ['id' => $outing->getId()]);
        }

        return $this->render('pages/outingManagement.html.twig', [
            'outingForm' => $form->createView(),
            'action' => 'create'
        ]);
    }

    #[Route('/modifier-une-sortie/{id}', name: 'outing_update')]
    public function update(Request $request, EntityManagerInterface $entityManager, ManagerRegistry $doctrine, int $id): Response
    {
        $outing = $doctrine->getRepository(Outing::class)->find($id);

        if (!$outing) {
            // Gérer le cas où la sortie n'existe pas
            // Par exemple, afficher une erreur ou rediriger ailleurs
        }

        $form = $this->createForm(OutingUpdateFormType::class, $outing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $outingImageFile = $form->get('outingImage')->getData();

            if ($outingImageFile instanceof UploadedFile) {
                $oldOutingImage = $outing->getOutingImage();

                if ($oldOutingImage) {
                    $oldOutingImagePath = $this->getParameter('outing_pictures_directory') . '/' . $oldOutingImage;
                    if (file_exists($oldOutingImagePath)) {
                        unlink($oldOutingImagePath);
                    }
                }

                $newFileName = md5(uniqid()) . '.' . $outingImageFile->guessExtension();

                $outingImageFile->move(
                    $this->getParameter('outing_pictures_directory'),
                    $newFileName
                );

                $outing->setOutingImage($newFileName);
            }

            $entityManager->persist($outing);
            $entityManager->flush();
        }

        return $this->render('pages/outingManagement.html.twig', [
            'outingForm' => $form->createView(),
            'action' => 'update',
            'outing' => $outing
        ]);
    }
}
