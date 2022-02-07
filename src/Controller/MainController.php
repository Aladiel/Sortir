<?php

namespace App\Controller;

use App\Service\CallApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class MainController extends AbstractController
{

    /**
     * @Route ("/", name="main_home")
     */
    public function home(CallApiService $callApiService): Response
    {
        return $this->render('main\home.html.twig', [
            'data' => $callApiService -> getVillesData()
    ]);
    }



}