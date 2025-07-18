<?php

namespace App\Controller\Front;

use App\Entity\Devis;
use App\Entity\Facture;
use App\Entity\Client;
use App\Form\FactureType;
use App\Repository\FactureRepository;
use App\Service\BrevoEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\StripeClient;
use stripe\Stripe;
use Doctrine\Common\Proxy\Proxy;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/facture')]
class FactureController extends AbstractController
{
    #[Route('/', name: 'app_facture_index', methods: ['GET'])]
    public function index(FactureRepository $factureRepository, EntityManagerInterface $entityManager): Response
    {

        $user = $this->getUser();
        $companyId = $user->getCompanyId();

        return $this->render('front/facture/index.html.twig', [
            'factures' => $factureRepository->findByCompagny($companyId),
        ]);
    }


    #[Route('/{id}', name: 'app_facture_show', methods: ['GET'])]
    public function show(Facture $facture, EntityManagerInterface $entityManager): Response
    {

        $categoriProduits = [];
        $tauxTVA = [];
        $total['ht'] = 0;

        foreach ($facture->getProduits() as $produit) {
            $categoryTemp = $produit['category']['name'];
            $categoriProduits[$categoryTemp][] = $produit;

            if(!isset($tauxTVA[$produit['tva']])){
                $tauxTVA[intval($produit['tva'])] = 0;
            }

            $tauxTVA[intval($produit['tva'])] += $produit['price'] * $produit['quantite'];
            $total['ht'] += $produit['price'] * $produit['quantite'];

            if(!isset($total['tva'][$produit['tva']])){
                $total['tva'][$produit['tva']] = 0;
            }
            $total['tva'][$produit['tva']] += $produit['prix_totale'] - ($produit['price'] * $produit['quantite']);
        }

        ksort($total['tva']);

        $total['ttc'] = $facture->getPrixTotal();

        $client = Client::arrayToClient($facture->getClient());

        return $this->render('front/facture/show.html.twig', [
            'facture' => $facture,
            'categoriProduits' => $categoriProduits,
            'tauxTVA' => $tauxTVA,
            'total' => $total,
            'client' => $client,
        ]);

    }



    #[Route('/{id}/paid', name: 'app_facture_paid', methods: ['GET', 'POST'])]
    public function paid(Request $request, Facture $facture, EntityManagerInterface $entityManager): Response
    {
        // Utiliser les variables d'environnement pour les clés Stripe
        $stripePublicKey = $this->getParameter('app.stripe.public_key');
        $stripe = new StripeClient($stripePublicKey);
        
        // Utiliser une variable d'environnement pour le domaine
        $domain = $this->getParameter('app.domain');
        
        $checkout_session = $stripe->checkout->sessions->create([
            'ui_mode' => 'embedded',
            'line_items' => [[
                'price' => '{{PRICE_ID}}',
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'return_url' => $domain . '/return.html?session_id={CHECKOUT_SESSION_ID}',
        ]);

        return new JsonResponse(['clientSecret' => $checkout_session->client_secret]);
    }

    #[Route('/{id}', name: 'app_facture_delete', methods: ['POST'])]
    public function delete(Request $request, Facture $facture, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$facture->getId(), $request->request->get('_token'))) {
            $entityManager->remove($facture);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_facture_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/download-pdf', name: 'app_facture_download_pdf', methods: ['GET'])]
    public function downloadPdf(Facture $facture): Response
    {
        $categoriProduits = [];
        $tauxTVA = [];
        $total['ht'] = 0;

        foreach ($facture->getProduits() as $produit) {
            $categoryTemp = $produit['category']['name'];
            $categoriProduits[$categoryTemp][] = $produit;

            if(!isset($tauxTVA[$produit['tva']])){
                $tauxTVA[intval($produit['tva'])] = 0;
            }

            $tauxTVA[intval($produit['tva'])] += $produit['price'] * $produit['quantite'];
            $total['ht'] += $produit['price'] * $produit['quantite'];

            if(!isset($total['tva'][$produit['tva']])){
                $total['tva'][$produit['tva']] = 0;
            }
            $total['tva'][$produit['tva']] += $produit['prix_totale'] - ($produit['price'] * $produit['quantite']);
        }

        ksort($total['tva']);

        $total['ttc'] = $facture->getPrixTotal();

        $client = Client::arrayToClient($facture->getClient());

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);

        $html = $this->renderView('front/facture/pdf_facture_template.html.twig', [
            'facture' => $facture,
            'categoriProduits' => $categoriProduits,
            'tauxTVA' => $tauxTVA,
            'total' => $total,
            'client' => $client,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $client = Client::arrayToClient($facture->getClient());
        $clientNameSafe = $client ? preg_replace('/[^A-Za-z0-9\-]/', '_', $client->getNom() . '_' . $client->getPrenom()) : 'Client_Inconnu';


        $pdfFileName = "devis_" . $clientNameSafe . "_" . $facture->getId()->toRfc4122() . ".pdf";


        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$pdfFileName.'"',
        ]);
    }

    #[Route('/{id}/send-facture-email', name: 'app_facture_send_email', methods: ['GET'])]
    public function sendFactureEmail(Facture $facture, BrevoEmailService $emailService, UrlGeneratorInterface $urlGenerator): Response
    {

        $categoriProduits = [];
        $tauxTVA = [];
        $total['ht'] = 0;

        foreach ($facture->getProduits() as $produit) {
            $categoryTemp = $produit['category']['name'];
            $categoriProduits[$categoryTemp][] = $produit;

            if(!isset($tauxTVA[$produit['tva']])){
                $tauxTVA[intval($produit['tva'])] = 0;
            }

            $tauxTVA[intval($produit['tva'])] += $produit['price'] * $produit['quantite'];
            $total['ht'] += $produit['price'] * $produit['quantite'];

            if(!isset($total['tva'][$produit['tva']])){
                $total['tva'][$produit['tva']] = 0;
            }
            $total['tva'][$produit['tva']] += $produit['prix_totale'] - ($produit['price'] * $produit['quantite']);
        }

        ksort($total['tva']);

        $total['ttc'] = $facture->getPrixTotal();

        $client = Client::arrayToClient($facture->getClient());

        $senderName = 'Plumbpay';
        $senderEmail = 'team_plumbpay@outlook.com';
        $recipientName = $client->getNom() . ' ' . $client->getPrenom();
        $recipientEmail = $client->getEmail();
        $factureNum = $facture->getNumFacture();
        $subject = 'Votre Facture ' . $factureNum . ' de Plumbpay';

        // Générer le contenu HTML du devis

        $htmlContent = $this->renderView('front/facture/pdf_facture_template.html.twig', [
            'facture' => $facture,
            'categoriProduits' => $categoriProduits,
            'tauxTVA' => $tauxTVA,
            'total' => $total,
            'client' => $client,
        ]);

        // Envoyer l'email
        $emailService->sendEmail(
            $senderName,
            $senderEmail,
            $recipientName,
            $recipientEmail,
            $subject,
            $htmlContent
        );
        $clientMail = $client->getEmail();
        $this->addFlash('success', 'La facture a été envoyé par email au client ('.$clientMail.').');

        return $this->redirectToRoute('front_app_facture_index');
    }
}
