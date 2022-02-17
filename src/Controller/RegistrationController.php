<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\User;
use App\Form\ProfilUpdateFormType;
use App\Form\RegistrationFormType;
use App\Form\SearchType;
use App\Repository\UserRepository;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/admin/register", name="app_register")
     */
    public function register(Request $request,
                             UserPasswordHasherInterface $userPasswordHasher,
                             UserAuthenticatorInterface $userAuthenticator,
                             AppAuthenticator $authenticator,
                             EntityManagerInterface $entityManager,
                             SluggerInterface $slugger): Response
    {
        $user = new User();
        $user->setRoles(["ROLE_USER"]);

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('photo')->getData();

            if ($file){
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
                $file->move(
                    $this->getParameter('upload_directory'),
                    $fileName
                );
                $user->setPhoto($fileName);
            }

            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            $this->addFlash('success', 'Le profil a bien été créé !');
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        } else if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('warning', 'Le profil n\'a pas été créé !');
        }

        return $this->render('admin/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/list", name="users_list", methods={"GET", "POST"})
     */
    public function list(UserRepository $userRepository, Request $request): Response
    {
        $data = new User();
        $form = $this->createForm(SearchType::class, $data);
        $form ->handleRequest($request);
        //$names =$userRepository->findSearch($data);

        return $this->render('admin/userslist.html.twig', [
            'form' => $form->createView(),
            'names' => $userRepository->findBy(
                ['nom' => 'Keyboard']
            ),
            'users' => $userRepository->findAll(),
            //'names' => $names
        ]);
    }

    /**
     * @Route("/details/{id}", name="details_profil")
     */
    public function details(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException("rien à afficher");
        }
        return $this->render('registration/detailsProfil.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/modifier/{id}", name="modifier_profil")
     */
    public function modifier(int $id, UserRepository $userRepository,
                             Request $request,
                             UserPasswordHasherInterface $userPasswordHasher,
                             UserAuthenticatorInterface $userAuthenticator,
                             AppAuthenticator $authenticator,
                             EntityManagerInterface $entityManager,
                             SluggerInterface $slugger): Response
    {
        $user = $userRepository->find($id);

        $profilUpdateForm = $this->createForm(ProfilUpdateFormType::class, $user);
        $profilUpdateForm->handleRequest($request);

        if ($profilUpdateForm->isSubmitted() && $profilUpdateForm->isValid()) {

            $newFile = $profilUpdateForm->get('newPhoto')->getData();

            if ($newFile){
                $originalFilename = pathinfo($newFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $fileName = $safeFilename.'-'.uniqid().'.'.$newFile->guessExtension();
                $newFile->move(
                    $this->getParameter('upload_directory'),
                    $fileName
                );
                $user->setPhoto(null);
                $user->setPhoto($fileName);
            }

            $password = $profilUpdateForm->get('plainPassword')->getData();
            if ($password){
                // encode the plain password
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $profilUpdateForm->get('plainPassword')->getData()
                    )
                );
                return $userAuthenticator->authenticateUser(
                    $user,
                    $authenticator,
                    $request
                );
            }

            $entityManager->flush();

            $this->addFlash('success', 'Le profil a bien été modifié !');
            return $this->redirectToRoute('details_profil', ['id'=>$user->getId()]);
        } else if ($profilUpdateForm->isSubmitted() && !$profilUpdateForm->isValid()) {
            $this->addFlash('warning', 'Le profil n\'a pas été modifié !');
        }
        return $this->render('registration/modifierProfil.html.twig', [
            'user' => $user,
            'profilUpdateForm' => $profilUpdateForm->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(User $user, EntityManagerInterface $entityManager): \Symfony\Component\HttpFoundation\RedirectResponse
    {

        $entityManager -> remove($user);
        $entityManager -> flush();

        $this->addFlash('success', 'Le profil a bien été supprimé !');
        return $this -> redirectToRoute('app_logout');

    }
}
