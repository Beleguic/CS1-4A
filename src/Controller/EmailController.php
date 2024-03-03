<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\BrevoEmailService;

class EmailController extends AbstractController
{
    #[Route('/send-mail', name: 'send-mail')]
    public function sendEmail(BrevoEmailService $emailService): Response
    {
        $senderName = 'PlombPay';
        $senderEmail = 'team_plombpay@outlook.com';
        $recipientName = 'John Doe';
        $recipientEmail = 'testmail@example.com';
        $subject = 'Email Test';
        $htmlContent = '<html><head></head><body><p>Hello,</p>This is my first transactional email sent from Brevo.</p></body></html>';

        $response = $emailService->sendEmail($senderName, $senderEmail, $recipientName, $recipientEmail, $subject, $htmlContent);

        return new Response($response);
    }
}