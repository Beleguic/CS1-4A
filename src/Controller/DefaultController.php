<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'default_index')]
    public function index(): Response
    {
        return $this->render('default/index.html.twig');
    }

    #[Route('/design', name: 'design_index')]
    public function design_index(): Response
    {
        return $this->render('design/index.html.twig');
    }
}
