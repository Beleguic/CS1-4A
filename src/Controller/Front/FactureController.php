<?php

namespace App\Controller\Front;

use App\Entity\Facture;
use App\Form\FactureType;
use App\Repository\FactureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\StripeClient;
use stripe\Stripe;

#[Route('/facture')]
class FactureController extends AbstractController
{
    #[Route('/', name: 'app_facture_index', methods: ['GET'])]
    public function index(FactureRepository $factureRepository): Response
    {
        return $this->render('front/facture/index.html.twig', [
            'factures' => $factureRepository->findAll(),
            
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

    #[Route('/{id}', name: 'app_facture_show', methods: ['GET'])]
    public function show(Facture $facture): Response
    {
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
