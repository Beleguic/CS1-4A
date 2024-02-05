<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'default_index')]
    public function index(): Response
    {
        return $this->render('front/default/index.html.twig');
    }

    #[Route('/design', name: 'design_index')]
    public function index_design(): Response
    {
        return $this->render('front/design/index.html.twig');
    }
}
