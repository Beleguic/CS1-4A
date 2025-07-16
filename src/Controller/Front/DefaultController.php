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
        return $this->render('front/index/index.html.twig');
    }

    #[Route('/design', name: 'design_index')]
    public function index_design(): Response
    {
        return $this->render('front/design/index.html.twig');
    }

    #[Route('/legals', name: 'legals_index')]
    public function index_legals(): Response
    {
        return $this->render('front/legals/index.html.twig');
    }

    #[Route('/icons-demo', name: 'app_icons_demo')]
    public function iconsDemo(): Response
    {
        return $this->render('front/icons_demo.html.twig');
    }
}
