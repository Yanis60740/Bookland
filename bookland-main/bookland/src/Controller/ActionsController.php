<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActionsController extends AbstractController
{
    /**
     * @Route("/actions", name="actions_index")
     */
    public function index(): Response
    {
        return $this->render('actions/index.html.twig', [
            'controller_name' => 'ActionsController',
        ]);
    }
}
