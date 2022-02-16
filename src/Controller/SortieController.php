<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\SortieType;
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


        $sortieForm -> handleRequest($request);

        if ($sortieForm -> isSubmitted() && $sortieForm -> isValid())  {
            $cpo = $sortieForm->get('codePostal')->getData();
            $nomVille = $sortieForm->get('ville')-> getData();
            $nomRue = $sortieForm->get('rue')-> getData();
            $latitude = $sortieForm->get('latitude')-> getData();
            $longitude = $sortieForm->get('longitude')-> getData();
            //dd($sortieForm);
            $ville -> setCodePostal($cpo);
            $ville -> setNom($nomVille);
            $lieu -> setRue($nomRue);
            $lieu -> setLatitude($latitude);
            $lieu -> setLongitude($longitude);

            $entityManager -> persist($sortie);
            $entityManager -> flush();


            $this -> addFlash('success', 'Sortie ajoutée');
        }


        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm -> createView()
        ]);
    }
}
