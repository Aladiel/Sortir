<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\LieuType;
use App\Form\SortieType;
use App\Form\VilleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/sortie", name="sortie_")
 */

class SortieController extends AbstractController
{
    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sortie = new Sortie();
        $ville = new Ville();
        $lieu = new Lieu();
        $sortieForm = $this -> createForm(SortieType::class, $sortie);
        $villeForm = $this -> createForm(VilleType::class, $ville);
        $lieuForm = $this -> createForm(LieuType::class, $lieu);


        $sortieForm -> handleRequest($request);
        $villeForm -> handleRequest($request);
        $lieuForm -> handleRequest($request);

        if (($sortieForm -> isSubmitted() && $sortieForm -> isValid()) && ($villeForm -> isSubmitted() && $villeForm -> isValid())) {
            $entityManager -> persist($sortie);
            $entityManager -> flush();
            $entityManager -> persist($ville);
            $entityManager -> flush();
            $entityManager -> persist($lieu);
            $entityManager -> flush();

            $this -> addFlash('success', 'Sortie ajoutée');
        }


        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm -> createView(),
            'villeForm' => $villeForm -> createView(),
            'lieuForm' => $lieuForm -> createView()
        ]);
    }
}
