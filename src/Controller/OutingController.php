<?php

namespace App\Controller;

use App\Entity\Outing;
use App\Form\OutingCancelFormType;
use App\Form\OutingCreateFormType;
use App\Form\OutingsSearchFormType;
use App\Form\OutingUpdateFormType;
use App\Repository\OutingRepository;
use App\Service\ImageManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Security;

class OutingController extends AbstractController
{
    #[Route('/', name: 'outing_home')]
    public function index(Request $request, OutingRepository $outingRepository): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $next20Outings = $outingRepository->findNext20Outings();
        $next20OutingsByUserCampus = $this->getUser() ? $outingRepository->findNext20OutingsByCampus($user->getCampus()) : null;
        $outingsByAuthor = $this->getUser() ? $outingRepository->findOutingsByAuthor($this->getUser()) : null;
        $outingsByRegistrant = $this->getUser() ?  $outingRepository->findOutingsByRegistrant($this->getUser()) : null;

        $form = $this->createForm(OutingsSearchFormType::class);
        $form->handleRequest($request);

        $outingsSearch = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $searchByCampus = $form->get('campus')->getData();
            $searchByName = $form->get('search')->getData();

            if ($searchByCampus && !$searchByName) {
                $outingsSearch = $outingRepository->findNext20OutingsByCampus($searchByCampus);
            }

            //This research is more important than the previous one. If both fields are filled, she win
            if ($searchByName) {
                $outingsSearch = $outingRepository->findByName($searchByName);
            }
        }

        return $this->render('pages/home.html.twig', [
            'next20Outings' => $next20Outings,
            'next20OutingsByUserCampus' => $next20OutingsByUserCampus,
            'outingsByAuthor' => $outingsByAuthor,
            'outingsByRegistrant' => $outingsByRegistrant,
            'searchForm' => $form->createView(),
            'outingsSearch' => $outingsSearch
        ]);
    }

    #[Route('/cree-une-sortie', name: 'outing_create')]
    #[IsGranted('ROLE_ANIMATOR')]
    public function create(Request $request, EntityManagerInterface $entityManager, ImageManager $imageManager): Response
    {
        $outing = new Outing();
        $form = $this->createForm(OutingCreateFormType::class, $outing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newFileName = $imageManager->saveImage($form->get('outingImage')->getData(), null, 'outing_pictures_directory');

            if ($newFileName) {
                $outing->setOutingImage($newFileName);
            }

            $outing->setAuthor($this->getUser());

            $entityManager->persist($outing);
            $entityManager->flush();

            return $this->redirectToRoute('outing_update', ['id' => $outing->getId()]);
        }

        return $this->render('pages/outingManagement.html.twig', [
            'outingForm' => $form->createView(),
            'outing' => 'create',
            'action' => 'create'
        ]);
    }

    #[Route('/modifier-une-sortie/{id}', name: 'outing_update')]
    #[IsGranted('ROLE_ANIMATOR')]
    public function update(Request $request, EntityManagerInterface $entityManager, OutingRepository $outingRepository, ImageManager $imageManager, Security $security, int $id): Response
    {
        $outing = $outingRepository->find($id);

        if (!$outing) {
            throw new NotFoundHttpException();
        }

        /** @var \App\Entity\User $user */
        $user = $security->getUser();

        if (!$security->isGranted('ROLE_ADMIN') && ($user->getId() !== $outing->getAuthor()->getId())) {
            throw new AccessDeniedHttpException();
        }

        $updateForm = $this->createForm(OutingUpdateFormType::class, $outing);
        $updateForm->handleRequest($request);

        if ($updateForm->isSubmitted() && $updateForm->isValid()) {
            $newFileName = $imageManager->saveImage($updateForm->get('outingImage')->getData(), $outing->getOutingImage(), 'outing_pictures_directory');

            if ($newFileName) {
                $outing->setOutingImage($newFileName);
            }

            $entityManager->persist($outing);
            $entityManager->flush();
        }

        $cancelForm = $this->createForm(OutingCancelFormType::class, $outing);
        $cancelForm->handleRequest($request);

        if ($cancelForm->isSubmitted() && $cancelForm->isValid()) {
            $entityManager->persist($outing);
            $entityManager->flush();
        }

        return $this->render('pages/outingManagement.html.twig', [
            'outingForm' => $updateForm->createView(),
            'outingCancelForm' => $cancelForm->createView(),
            'action' => 'update',
            'outing' => $outing
        ]);
    }

    #[Route('/sortie/{id}', name: 'outing_show')]
    public function selectById(OutingRepository $outingRepository, int $id)
    {
        $outing = $outingRepository->find($id);
        $now = new \DateTime('now');

        if (!$outing) {
            throw new NotFoundHttpException();
        }

        return $this->render('pages/outingShow.html.twig', [
            'outing' => $outing,
            'now' => $now
        ]);
    }

    #[Route('/sortie/{id}/inscription', name: 'outing_subscribe')]
    #[IsGranted('ROLE_USER')]
    public function subscribe(OutingRepository $outingRepository, EntityManagerInterface $entityManager, int $id)
    {
        $outing = $outingRepository->find($id);
        $now = new \DateTime('now');

        if (!$outing) {
            throw new NotFoundHttpException();
        }

        if ($outing->getRegistrants()->count() < $outing->getNumberPlaces() && $outing->getRegistrationDeadline() > new \DateTime('now')) {
            $outing->addRegistrant($this->getUser());

            $entityManager->persist($outing);
            $entityManager->flush();
        }


        return $this->render('pages/outingShow.html.twig', [
            'outing' => $outing,
            'now' => $now
        ]);
    }

    #[Route('/sortie/{id}/desinscription', name: 'outing_unsubscribe')]
    #[IsGranted('ROLE_USER')]
    public function unsubscribe(OutingRepository $outingRepository,  EntityManagerInterface $entityManager, int $id)
    {
        $outing = $outingRepository->find($id);

        if (!$outing) {
            throw new NotFoundHttpException();
        }

        if ($outing->getRegistrationDeadline() > new \DateTime('now')) {
            $outing->removeRegistrant($this->getUser());

            $entityManager->persist($outing);
            $entityManager->flush();
        }

        $now = new \DateTime('now');

        return $this->render('pages/outingShow.html.twig', [
            'outing' => $outing,
            'now' => $now
        ]);
    }

    #[Route('/sortie/{id}/maintenir', name: 'outing_removeCancelReason')]
    #[IsGranted('ROLE_ANIMATOR')]
    public function removeCancelReason(OutingRepository $outingRepository,  EntityManagerInterface $entityManager, int $id)
    {
        $outing = $outingRepository->find($id);

        if (!$outing) {
            throw new NotFoundHttpException();
        }

        $outing->setCancelReason(null);

        $entityManager->persist($outing);
        $entityManager->flush();

        $now = new \DateTime('now');


        return $this->render('pages/outingShow.html.twig', [
            'outing' => $outing,
            'now' => $now
        ]);
    }
}
