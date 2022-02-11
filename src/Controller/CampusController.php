<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use App\Repository\CampusRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/campus", name="campus_")
 */
class CampusController extends AbstractController
{
    /**
     * @Route("/", name="list", methods={"GET"})
     */
    public function list(CampusRepository $campusRepository): Response
    {
        return $this->render('admin/campus/list.html.twig', [
            'campuses' => $campusRepository->findAll()
        ]);
    }

    /**
     * @Route("/creer", name="creer", methods={"GET", "POST"})
     */
    public function creer(Request $request,
                          EntityManagerInterface $entityManager): Response
    {
        $campus = new Campus();

        $campusForm = $this->createForm(CampusType::class, $campus);
        $campusForm->handleRequest($request);

        if ($campusForm->isSubmitted() && $campusForm->isValid()) {

            $entityManager->persist($campus);
            $entityManager->flush();

            $this->addFlash('success', 'Le campus a bien été créé !');
            return $this->redirectToRoute('campus_details', ['id' => $campus->getId()]);
        } else if ($campusForm->isSubmitted() && !$campusForm->isValid()) {
            $this->addFlash('warning', 'Le campus n\'a pas été créé !');
        }

        return $this->render('admin/campus/creer.html.twig', [
            'campusForm'=> $campusForm->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="details", methods={"GET"})
     */
    public function details(Campus $campus): Response
    {
        return $this->render('admin/campus/details.html.twig', [
            'campus' => $campus
        ]);
    }

    /**
     * @Route("/{id}/modifier", name="modifier", methods={"GET", "POST"})
     */
    public function modifier(Request $request, Campus $campus,
                             EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le campus a bien été modifié !');
            return $this->redirectToRoute('campus_list', [],
                Response::HTTP_SEE_OTHER);
        } else if ($form->isSubmitted() && !$form->isValid()){
            $this->addFlash('warning', 'Le campus n\'a pas été modifié !');
        }

        return $this->renderForm('admin/campus/modifier.html.twig', [
            'campus' => $campus,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="supprimer", methods={"POST"})
     */
    public function supprimer(Request $request, int $id,
                              CampusRepository $campusRepository,
                              EntityManagerInterface $entityManager): Response
    {
        $campus = new Campus();
        $campus = $campusRepository->find($id);

        try {
            $entityManager->remove($campus);
            $entityManager->flush();

            $this->addFlash('success', 'Le campus a bien été supprimé !');
            return $this->redirectToRoute('campus_list', [],
                Response::HTTP_SEE_OTHER);
        } catch (ForeignKeyConstraintViolationException $f) {
            $this->addFlash('foreignkey_constraint_violation', sprintf(
                'Le campus ne peut pas être supprimé car il est lié à un alumnus - %s',
                $f->getMessage()
            ));

            return $this->redirectToRoute('campus_list');
        }
    }

}











