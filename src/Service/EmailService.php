<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }


    //Tester les emails avant !

    public function sendTestEmail()
    {
        $email = (new Email())
            ->from('test@test.com')
            ->to('test_email@test.com')
            ->subject('Test Email')
            ->text('This is a test email from your Symfony application.');

        $this->mailer->send($email);
    }

    //Test mail Prod 

    public function sendInvoiceEmail($clientEmail, $invoiceUrl)
    {
        $email = (new Email())
            ->from('your_email@test.com')
            ->to('to@example.com')
            //->to($clientEmail)
            ->subject('Invoice')
            ->text('Dear client, here is your invoice: '.$invoiceUrl);

        $this->mailer->send($email);
    }

}