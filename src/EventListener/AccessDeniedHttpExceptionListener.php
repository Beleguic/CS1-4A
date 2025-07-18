<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AccessDeniedHttpExceptionListener
{
    public function onKernelException(ExceptionEvent $event):void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof AccessDeniedHttpException) {
            // Rediriger vers la page de connexion au lieu de révéler l'existence de ressources
            $response = new RedirectResponse('/login');
            $event->setResponse($response);
        }
    }
}