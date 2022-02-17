<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Ville;
use App\Form\SortieCancelType;
use App\Form\SortieCreerType;
use App\Form\SortieType;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
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
    /**
     * @Route("/", name="list", methods={"GET"})
     */
    public function list(SortieRepository $sortieRepository): Response
    {
        return $this->render('sortie/list.html.twig', [
            'sorties' => $sortieRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{idu}/{idl}/creer", name="creer", methods={"GET", "POST"})
     */
    public function creer(Request $request, int $idu, int $idl,
                          UserRepository $userRepository,
                          LieuRepository $lieuRepository,
                          EntityManagerInterface $entityManager): Response
    {
        $sortie = new Sortie();

        $user = new User();
        $user = $userRepository->find($idu);

        $lieu = new Lieu();
        $lieu = $lieuRepository->find($idl);

        $sortieForm = $this->createForm(SortieCreerType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid())
        {
            $sortie->setOrganisateur($user->getPrenom());
            $sortie->setLieu($lieu);
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'La sortie a bien été créée');
            return $this->redirectToRoute('main_home');
        } elseif ($sortieForm->isSubmitted() && !$sortieForm->isValid())
        {
            $this->addFlash('warning', 'La sortie n\'a pas été créée');
        }
        return $this->render('sortie/creer.html.twig', [
            'sortieForm' => $sortieForm->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="details", methods={"GET"})
     */
    public function details(Sortie $sortie): Response
    {
        return $this->render('sortie/details.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    /**
     * @Route("/{id}/modifier", name="modifer", methods={"GET", "POST"})
     */
    public function modifier(Request $request, Sortie $sortie,
                             EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SortieCreerType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager->flush();

            $this->addFlash('success', 'La sortie a bien été modifiée !');
            return $this->redirectToRoute('sortie_details', ['id' => $sortie->getId()]);
        } elseif ($form->isSubmitted() && !$form->isValid())
        {
            $this->addFlash('warning', 'La sortie n\a pas été modifiée !');
        }
        return $this->renderForm('sortie/modifier.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="supprimer", methods={"POST"})
     */
    public function supprimer(Request $request, int $id,
                              SortieRepository $sortieRepository,
                              EntityManagerInterface $entityManager): Response
    {
        $sortie = new Sortie();
        $sortie = $sortieRepository->find($id);

        try {
            $entityManager->remove($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'La sortie a bien été supprimée !');
            return $this->redirectToRoute('main_home', [],
                Response::HTTP_SEE_OTHER);
        } catch (ForeignKeyConstraintViolationException $f) {
            $this->addFlash('foreignkey_constraint_violation', sprintf(
                'La sortie ne peut pas être supprimé car elle contient des participants - 
                foreignkey_constraint_violation : Code %s',
                $f->getCode()
            ));

            return $this->redirectToRoute('sortie_details', ['id' => $sortie->getId()]);
        }
    }

    /**
     * @Route("/{id}/{idu}/participer", name="participer", methods={"POST"})
     */
    public function participer(Request $request, int $id, int $idu,
                               SortieRepository $sortieRepository,
                               UserRepository $userRepository,
                               EntityManagerInterface $entityManager): Response
    {
        $sortie = new Sortie();
        $sortie = $sortieRepository->find($id);

        $user = new User();
        $user = $userRepository->find($idu);

        if (!$sortie->getUsers()->contains($user))
        {
            $sortie->addUser($user);
            $nbP = $sortie->getNbInscriptionMax() - 1;
            $sortie->setNbInscriptionMax($nbP);
            $entityManager->flush();

            $this->addFlash('success', 'Votre participation est bien prise en compte !');
        } else
        {
            $this->addFlash('warning', 'Action refusée : Vous participez déjà à cette sortie !');
        }

        return $this->redirectToRoute('sortie_details', ['id' => $sortie->getId()]);
    }

    /**
     * @Route("/{id}/{idu}/desister", name="desister", methods={"POST"})
     */
    public function desister(Request $request, int $id, int $idu,
                             SortieRepository $sortieRepository,
                             UserRepository $userRepository,
                             EntityManagerInterface $entityManager): Response
    {
        $sortie = new Sortie();
        $sortie = $sortieRepository->find($id);

        $user = new User();
        $user = $userRepository->find($idu);

        if ($sortie->getUsers()->contains($user))
        {
            $sortie->removeUser($user);
            $nbP = $sortie->getNbInscriptionMax() + 1;
            $sortie->setNbInscriptionMax($nbP);
            $entityManager->flush();

            $this->addFlash('success', 'Vous avez bien été retiré de cette sortie !');
        } else
        {
            $this->addFlash('warning', 'Action refusée : Vous ne participez pas à cette sortie !');
        }

        return $this->redirectToRoute('sortie_details', ['id' => $sortie->getId()]);
    }

    /**
     * @Route("/{id}/publier", name="publier", methods={"POST"})
     */
    public function publier(Request $request, int $id,
                            SortieRepository $sortieRepository,
                            EntityManagerInterface $entityManager): Response
    {
        $sortie = new Sortie();
        $sortie = $sortieRepository->find($id);

        $published = $sortie->getPublished();

        if ($published === false || $published === null)
        {
            $sortie->setPublished(true);
            $entityManager->flush();

            $this->addFlash('success', 'Votre sortie a bien été publiée !');
        } else
        {
            $this->addFlash('warning', 'Action refusée : Votre sortie est déjà publiée !');
        }

        return $this->redirectToRoute('main_home');
    }

    /**
     * @Route("/{id}/annuler", name="annuler", methods={"GET", "POST"})
     */
    public function annuler(Request $request, Sortie $sortie,
                            EntityManagerInterface $entityManager): Response
    {
        $cancelForm = $this->createForm(SortieCancelType::class, $sortie);
        $cancelForm->handleRequest($request);

        if ($cancelForm->isSubmitted() && $cancelForm->isValid())
        {
            $sortie->setCanceled(true);
            $entityManager->flush();

            $this->addFlash('success', 'La sortie a bien été annulée !');
            return $this->redirectToRoute('sortie_details', ['id' => $sortie->getId()]);
        } elseif ($cancelForm->isSubmitted() && !$cancelForm->isValid())
        {
            $this->addFlash('warning', 'La sortie n\a pas été annulée !');
        }
        return $this->renderForm('sortie/annuler.html.twig', [
            'sortie' => $sortie,
            'cancelForm' => $cancelForm,
        ]);
    }
}
