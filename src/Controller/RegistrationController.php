<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfilUpdateFormType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $user->setRoles(["ROLE_USER"]);

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
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
                             Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->find($id);

        $profilUpdateForm = $this->createForm(ProfilUpdateFormType::class, $user);
        $profilUpdateForm->handleRequest($request);

        if ($profilUpdateForm->isSubmitted() && $profilUpdateForm->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le profil a bien été modifié !');
            return $this->redirectToRoute('details_profil', ['id'=>$user->getId()]);
        }
        $this->addFlash('warning', 'Le profil n\'a pas été modifié !');
        return $this->render('registration/modifierProfil.html.twig', [
            'user' => $user,
            'profilUpdateForm' => $profilUpdateForm->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(User $user, EntityManagerInterface $entityManager) {
        $entityManager -> remove($user);
        $entityManager -> flush();

        return $this -> redirectToRoute('app_login');
    }
}
