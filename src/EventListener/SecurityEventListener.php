<?php

namespace App\EventListener;

use App\Service\SecurityLoggerService;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: InteractiveLoginEvent::class, method: 'onSecurityInteractiveLogin')]
#[AsEventListener(event: LogoutEvent::class, method: 'onSecurityLogout')]
class SecurityEventListener
{
    public function __construct(
        private SecurityLoggerService $securityLogger
    ) {}

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();
        $this->securityLogger->logLoginAttempt($user->getUserIdentifier(), true);
    }

    public function onSecurityLogout(LogoutEvent $event): void
    {
        $user = $event->getToken()?->getUser();
        if ($user) {
            $this->securityLogger->logSensitiveAction($user, 'logout');
        }
    }
} 