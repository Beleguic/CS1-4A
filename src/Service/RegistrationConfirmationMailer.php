<?php

namespace App\Service;

use Brevo\Mailer\MailerInterface;

class RegistrationConfirmationMailer
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendConfirmationEmail($recipientEmail)
    {
        $message = $this->mailer->createMessage()
            ->setTo($recipientEmail)
            ->setSubject('Confirmation de votre inscription')
            ->setBody('Bonjour, merci de votre inscription.');

        $this->mailer->send($message);
    }
}

