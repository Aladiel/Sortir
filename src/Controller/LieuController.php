<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/lieu", name="lieu_")
 */
class LieuController extends AbstractController
{
    /**
     * @Route("/", name="list", methods={"GET"})
     */
    public function list(LieuRepository $lieuRepository): Response
    {
        return $this->render('lieu/list.html.twig', [
            'lieux' => $lieuRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{idv}/creer", name="creer", methods={"GET", "POST"})
     */
    public function lieuCreer(Request $request, EntityManagerInterface $entityManager,
                                int $idv, VilleRepository $villeRepository,
                                UserInterface $user): Response
    {
        $lieu = new Lieu();

        $ville = new Ville();
        $ville = $villeRepository->find($idv);

        $lieuForm = $this->createForm(LieuType::class, $lieu);
        $lieuForm->handleRequest($request);

        if ($lieuForm->isSubmitted() && $lieuForm->isValid())
        {
            $lieu->setVille($ville);
            $entityManager->persist($lieu);
            $entityManager->flush();

            $this->addFlash('success', 'Le lieu a bien été créée');

            return $this->redirectToRoute('sortie_creer', [
                'idu' => $user->getId(),
                'idl' => $lieu->getId()
            ]);
        } elseif ($lieuForm->isSubmitted() && !$lieuForm->isValid())
        {
            $this->addFlash('warning', 'Le lieu n\'a pas été créée');
        }
        return $this->render('lieu/creer.html.twig', [
            'lieuForm' => $lieuForm->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="details", methods={"GET"})
     */
    public function details(Lieu $lieu): Response
    {
        return $this->render('lieu/details.html.twig', [
            'lieu' => $lieu,
        ]);
    }

    /**
     * @Route("/{id}/modifier", name="modifer", methods={"GET", "POST"})
     */
    public function modifier(Request $request, Lieu $lieu,
                             EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager->flush();

            $this->addFlash('success', 'Le lieu a bien été modifiée !');
            return $this->redirectToRoute('lieu_details', ['id' => $lieu->getId()]);
        } elseif ($form->isSubmitted() && !$form->isValid())
        {
            $this->addFlash('warning', 'Le lieu n\a pas été modifiée !');
        }
        return $this->renderForm('lieu/modifier.html.twig', [
            'lieu' => $lieu,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="supprimer", methods={"POST"})
     */
    public function supprimer(Request $request, int $id,
                              LieuRepository $lieuRepository,
                              EntityManagerInterface $entityManager): Response
    {
        $lieu = new Lieu();
        $lieu = $lieuRepository->find($id);

        try {
            $entityManager->remove($lieu);
            $entityManager->flush();

            $this->addFlash('success', 'Le lieu a bien été supprimée !');
            return $this->redirectToRoute('lieu_creer', [
                'idv' => $lieu->getVille()->getId()
            ],
                Response::HTTP_SEE_OTHER);
        } catch (ForeignKeyConstraintViolationException $f) {
            $this->addFlash('foreignkey_constraint_violation', sprintf(
                'Le lieu ne peut pas être supprimé car il est lié à une ou plusieurs sorties - 
                foreignkey_constraint_violation : Code %s',
                $f->getCode()
            ));

            return $this->redirectToRoute('lieu_details', ['id' => $lieu->getId()]);
        }
    }

}
