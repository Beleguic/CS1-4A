<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\Response;

class AccessDeniedHttpExceptionListener
{
    public function onKernelException(ExceptionEvent $event):void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof AccessDeniedHttpException) {
            // Change the response to 404
            $response = new Response('Not Found', Response::HTTP_NOT_FOUND);
            $event->setResponse($response);
        }
    }
}