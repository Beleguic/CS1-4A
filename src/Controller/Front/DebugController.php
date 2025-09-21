<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/debug')]
class DebugController extends AbstractController
{
    #[Route('/session', name: 'debug_session')]
    public function sessionDebug(RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        $user = $this->getUser();
        
        $debugInfo = [
            'session_id' => $session->getId(),
            'session_started' => $session->isStarted(),
            'user_connected' => $user ? $user->getEmail() : 'Non connecté',
            'user_roles' => $user ? $user->getRoles() : [],
            'session_data' => $session->all(),
            'session_metadata' => $session->getMetadataBag()->all(),
        ];
        
        return new Response('<pre>' . print_r($debugInfo, true) . '</pre>');
    }
}
