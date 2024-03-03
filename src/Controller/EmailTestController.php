<?php

namespace App\Controller;

use App\Service\EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/email-test')]
class EmailTestController extends AbstractController
{
    #[Route('/send-test', name: 'email_test_send_test', methods: ['GET'])]
    public function sendTestEmail(EmailService $emailService): Response
    {
        $emailService->sendTestEmail();


        $this->addFlash('success', 'Test email sent successfully.');

        //return $this->redirectToRoute('email_test_send_test');
        return $this->render('email_test/index.html.twig', [
         
        ]);
    }
}
