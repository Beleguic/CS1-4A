<?php

namespace App\Controller\Front;

use App\Entity\Facture;
use App\Entity\Client;
use App\Form\FactureType;
use App\Repository\FactureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\StripeClient;
use stripe\Stripe;
use Doctrine\Common\Proxy\Proxy;

#[Route('/facture')]
class FactureController extends AbstractController
{
    #[Route('/', name: 'app_facture_index', methods: ['GET'])]
    public function index(FactureRepository $factureRepository, EntityManagerInterface $entityManager): Response
    {

        $factures = $factureRepository->findAll();

        foreach ($factures as $facture) {
            $clientId = $facture->getClient()->getId();
            $client = $entityManager->find(Client::class, $clientId);

            if ($client instanceof Proxy) {
                // Force le chargement complet du proxy s'il n'a pas encore été chargé
                $entityManager->refresh($client);
            }
        }



        /*$clientId = $factureRepository->getClient()->getId();
        $client = $entityManager->find(Client::class, $clientId);

        if ($client instanceof Proxy) {
            // Force le chargement complet du proxy s'il n'a pas encore été chargé
            $entityManager->refresh($client);
        }*/

        dump($factures);

        return $this->render('front/facture/index.html.twig', [
            'factures' => $factures,
        ]);
    }

    #[Route('/new', name: 'app_facture_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $facture = new Facture();
        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $devis = $facture->getDevis();
            $devis->addFacture($facture);

            // Persistez l'entité Devis avant la facture
            $entityManager->persist($devis);

            // Persistez l'entité Facture
            $entityManager->persist($facture);
            $entityManager->flush();

            return $this->redirectToRoute('front_app_facture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('front/facture/new.html.twig', [
            'facture' => $facture,
            'form' => $form->createView(),
    ]);
    }

    #[Route('/{id}/send', name: 'app_facture_send', methods: ['GET', 'POST'])]
    public function send(Request $request, Facture $facture, EntityManagerInterface $entityManager): Response
    {
        // Genere un PDF avec les donnée de la facture
        // Envoyer le PDF par mail au client via son adrsse mail
        // Mettre a jour la facture pour dire qu'elle a été envoyé


        return 0;
    }

    #[Route('/{id}', name: 'app_facture_show', methods: ['GET'])]
    public function show(Facture $facture, EntityManagerInterface $entityManager): Response
    {
        $clientId = $facture->getClient()->getId();
        $client = $entityManager->find(Client::class, $clientId);

        if ($client instanceof Proxy) {
            // Force le chargement complet du proxy s'il n'a pas encore été chargé
            $entityManager->refresh($client);
        }

        dump($facture);

        return $this->render('front/facture/show.html.twig', [
            'facture' => $facture,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_facture_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Facture $facture, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('front_app_facture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('front/facture/edit.html.twig', [
            'facture' => $facture,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/paid', name: 'app_facture_paid', methods: ['GET', 'POST'])]
    public function paid(Request $request, Facture $facture, EntityManagerInterface $entityManager): Response
    {
        $stripe = new StripeClient('pk_test_51Oh8R5Cl4sk51Mu5Gd3FJCty3MICA3ZLW4HajUes3WoTbx0wgjgHoziMOWStmJlyV2AZAEIP8zugif9IFyNyXVrL00BK7EuAfr');
        header('Content-Type: application/json');

        $YOUR_DOMAIN = 'http://localhost:4242';

        $checkout_session = $stripe->checkout->sessions->create([
        'ui_mode' => 'embedded',
        'line_items' => [[
            'price' => '{{PRICE_ID}}',
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'return_url' => $YOUR_DOMAIN . '/return.html?session_id={CHECKOUT_SESSION_ID}',
        ]);

        echo json_encode(array('clientSecret' => $checkout_session->client_secret));
        return $this->render('front/facture/paid.html.twig', [
            'facture' => $facture,
        ]);
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
}
