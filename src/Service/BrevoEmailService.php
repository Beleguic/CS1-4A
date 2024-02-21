<?php

namespace App\Service;

use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
class BrevoEmailService
{
    private $client;
    private $apiKey;
    public function __construct(ParameterBagInterface $params)
    {
        $this->apiKey = $params->get('brevo_api_key');
        $this->client = new Client([
            'base_uri' => 'https://api.brevo.com/v3/smtp/',
            'headers' => [
                'accept' => 'application/json',
                'api-key' => $this->apiKey,
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