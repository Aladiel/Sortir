<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ville", name="ville_")
 */
class VilleController extends AbstractController
{
    /**
     * @Route("/", name="list", methods={"GET"})
     */
    public function list(VilleRepository $villeRepository): Response
    {
        return $this->render('ville/list.html.twig', [
            'villes' => $villeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/creer", name="creer", methods={"GET", "POST"})
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
            return $this->redirectToRoute('lieu_creer', [
                'idv' => $ville->getId()
            ]);
        } elseif ($villeForm->isSubmitted() && !$villeForm->isValid())
        {
            $this->addFlash('warning', 'La ville n\'a pas été créée');
        }
        return $this->render('ville/creer.html.twig', [
            'villeForm' => $villeForm->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="details", methods={"GET"})
     */
    public function details(Ville $ville): Response
    {
        return $this->render('ville/details.html.twig', [
            'ville' => $ville,
        ]);
    }

    /**
     * @Route("/{id}/modifier", name="modifer", methods={"GET", "POST"})
     */
    public function modifier(Request $request, Ville $ville,
                             EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager->flush();

            $this->addFlash('success', 'La ville a bien été modifiée !');
            return $this->redirectToRoute('ville_details', ['id' => $ville->getId()]);
        } elseif ($form->isSubmitted() && !$form->isValid())
        {
            $this->addFlash('warning', 'La sortie n\a pas été modifiée !');
        }
        return $this->renderForm('ville/modifier.html.twig', [
            'ville' => $ville,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="supprimer", methods={"POST"})
     */
    public function supprimer(Request $request, int $id,
                              VilleRepository $villeRepository,
                              EntityManagerInterface $entityManager): Response
    {
        $ville = new Ville();
        $ville = $villeRepository->find($id);

        try {
            $entityManager->remove($ville);
            $entityManager->flush();

            $this->addFlash('success', 'La ville a bien été supprimée !');
            return $this->redirectToRoute('ville_creer', [],
                Response::HTTP_SEE_OTHER);
        } catch (ForeignKeyConstraintViolationException $f) {
            $this->addFlash('foreignkey_constraint_violation', sprintf(
                'La ville ne peut pas être supprimé car elle est lié à un lieu - 
                foreignkey_constraint_violation : Code %s',
                $f->getCode()
            ));

            return $this->redirectToRoute('ville_details', ['id' => $ville->getId()]);
        }
    }

}
