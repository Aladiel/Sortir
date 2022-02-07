<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/Villes", name="villes")
     */
    public function villes(): Response
    {
        return $this->render('admin/villes.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/Campus", name="campus")
     */
    public function campus(): Response
    {
        return $this->render('admin/campus.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}
