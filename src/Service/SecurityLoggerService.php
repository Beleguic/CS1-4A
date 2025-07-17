<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;

class SecurityLoggerService
{
    public function __construct(
        private LoggerInterface $logger,
        private RequestStack $requestStack
    ) {}

    public function logLoginAttempt(string $email, bool $success, ?string $ip = null): void
    {
        $ip = $ip ?? $this->getClientIp();
        $message = $success ? 'Connexion réussie' : 'Tentative de connexion échouée';
        
        $this->logger->info('Login attempt', [
            'email' => $email,
            'success' => $success,
            'ip' => $ip,
            'user_agent' => $this->getUserAgent(),
            'timestamp' => new \DateTime(),
        ]);
    }

    public function logPasswordReset(string $email, bool $success): void
    {
        $this->logger->info('Password reset attempt', [
            'email' => $email,
            'success' => $success,
            'ip' => $this->getClientIp(),
            'timestamp' => new \DateTime(),
        ]);
    }

    public function logSensitiveAction(UserInterface $user, string $action, array $context = []): void
    {
        $this->logger->warning('Sensitive action performed', [
            'user_id' => $user->getUserIdentifier(),
            'action' => $action,
            'ip' => $this->getClientIp(),
            'context' => $context,
            'timestamp' => new \DateTime(),
        ]);
    }

    public function logAccessDenied(string $route, ?UserInterface $user = null): void
    {
        $this->logger->warning('Access denied', [
            'route' => $route,
            'user' => $user ? $user->getUserIdentifier() : 'anonymous',
            'ip' => $this->getClientIp(),
            'timestamp' => new \DateTime(),
        ]);
    }

    private function getClientIp(): string
    {
        $request = $this->requestStack->getCurrentRequest();
        return $request ? $request->getClientIp() : 'unknown';
    }

    private function getUserAgent(): string
    {
        $request = $this->requestStack->getCurrentRequest();
        return $request ? $request->headers->get('User-Agent', 'unknown') : 'unknown';
    }
} 