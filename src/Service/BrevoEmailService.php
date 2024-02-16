<?php

namespace App\Service;

use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BrevoEmailService
{
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.brevo.com/v3/smtp/',
            'headers' => [
                'accept' => 'application/json',
                'api-key' => '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',
                'content-type' => 'application/json',
            ],
        ]);
    }

    public function sendEmail($senderName, $senderEmail, $recipientName, $recipientEmail, $subject, $htmlContent)
    {
        $payload = [
            'sender' => [
                'name' => $senderName,
                'email' => $senderEmail,
            ],
            'to' => [
                [
                    'email' => $recipientEmail,
                    'name' => $recipientName,
                ],
            ],
            'subject' => $subject,
            'htmlContent' => $htmlContent,
        ];
    
        try {
            $response = $this->client->request('POST', 'email', [
                'json' => $payload,
            ]);
    
            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
    
            if ($statusCode >= 200 && $statusCode < 300) {
                // La demande a été réussie, le courriel a été envoyé
                return [
                    'success' => true,
                    'response_body' => $responseBody,
                ];
            } else {
                // La demande a échoué, le courriel n'a pas été envoyé
                return [
                    'success' => false,
                    'error' => 'Erreur lors de l\'envoi du courriel. Statut HTTP : ' . $statusCode,
                ];
            }
        } catch (\Exception $e) {
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}