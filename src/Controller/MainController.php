<?php

namespace App\Controller;

use App\Service\CallApiService;
use http\Client\Curl\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function mysql_xdevapi\getSession;


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