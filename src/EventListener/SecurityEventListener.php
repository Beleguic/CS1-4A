<?php

namespace App\EventListener;

use App\Service\SecurityLoggerService;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: InteractiveLoginEvent::class)]
#[AsEventListener(event: LogoutEvent::class)]
class SecurityEventListener
{
    public function __construct(
        private SecurityLoggerService $securityLogger
    ) {}

    public function onInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();
        $this->securityLogger->logLoginAttempt($user->getUserIdentifier(), true);
    }

    public function onLogout(LogoutEvent $event): void
    {
        $user = $event->getToken()?->getUser();
        if ($user) {
            $this->securityLogger->logSensitiveAction($user, 'logout');
        }
    }
} 