<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\User;
use App\Entity\Ville;
use App\Form\LieuType;
use App\Form\VilleType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LieuController extends AbstractController
{
    /**
     * @Route("/ville", name="ville_creer", methods={"GET", "POST"})
     */
    public function villeCreer(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ville = new Ville();

        $villeForm = $this->createForm(VilleType::class, $ville);
        $villeForm->handleRequest($request);

        if ($villeForm->isSubmitted() && $villeForm->isValid())
        {
            $entityManager->persist($ville);
            $entityManager->flush();

            $this->addFlash('success', 'La ville a bien été créée');
            return $this->redirectToRoute('lieu_creer');
        } elseif ($villeForm->isSubmitted() && !$villeForm->isValid())
        {
            $this->addFlash('warning', 'La ville n\'a pas été créée');
        }
        return $this->render('ville/creer.html.twig', [
            'villeForm' => $villeForm->createView()
        ]);
    }

    /**
     * @Route("/lieu", name="lieu_creer", methods={"GET", "POST"})
     */
    public function lieuCreer(Request $request, EntityManagerInterface $entityManager,
                                UserRepository $userRepository): Response
    {
        $lieu = new Lieu();

        $lieuForm = $this->createForm(LieuType::class, $lieu);
        $lieuForm->handleRequest($request);

        if ($lieuForm->isSubmitted() && $lieuForm->isValid())
        {
            $entityManager->persist($lieu);
            $entityManager->flush();

            $this->addFlash('success', 'Le lieu a bien été créée');

            return $this->redirectToRoute('lieu_creer');
        } elseif ($lieuForm->isSubmitted() && !$lieuForm->isValid())
        {
            $this->addFlash('warning', 'Le lieu n\'a pas été créée');
        }
        return $this->render('lieu/creer.html.twig', [
            'lieuForm' => $lieuForm->createView()
        ]);
    }
}
