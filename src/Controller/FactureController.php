<?php

namespace App\Controller;

use App\Service\EmailService;
use App\Entity\Facture;
use App\Entity\Client;
use App\Form\FactureType;
use App\Repository\FactureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[Route('/facture')]
class FactureController extends AbstractController
{

    private $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    #[Route('/', name: 'app_facture_index', methods: ['GET'])]
    public function index(FactureRepository $factureRepository): Response
    {
        return $this->render('facture/index.html.twig', [
            'factures' => $factureRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_facture_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
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

            $email = (new Email())
            ->from('hello@example.com')
            ->to('you@example.com')
            ->cc('cc@example.com')
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

            $mailer->send($email);

                return $this->redirectToRoute('app_facture_index', [], Response::HTTP_SEE_OTHER);
            }

        return $this->render('facture/new.html.twig', [
            'facture' => $facture,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_facture_show', methods: ['GET'])]
    public function show(Facture $facture): Response
    {
        return $this->render('facture/show.html.twig', [
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

            return $this->redirectToRoute('app_facture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('facture/edit.html.twig', [
            'facture' => $facture,
            'form' => $form,
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

    // Partie envoie des Email Automatiques

    #[Route('/{id}/send-email', name: 'app_facture_send_email', methods: ['GET'])]
    public function sendFactureEmail(EmailService $emailService, Facture $facture): Response
    {
        // Récupérez le devis associé à la facture
        $devis = $facture->getDevis();

        // Check si le devis existe
        if ($devis) {
            // Récupérez le nom du client à partir du devis
            $clientName = $devis->getClientName();

            $invoiceUrl = $this->generateUrl('app_facture_show', ['id' => $facture->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

            // Envoyez l'e-mail
            $emailService->sendInvoiceEmail($clientName, $invoiceUrl);

            $this->addFlash('success', 'L\'e-mail de la facture a été envoyé avec succès.');

            return $this->redirectToRoute('app_facture_show', ['id' => $facture->getId()]);
        }

        // Gérez le cas où la facture n'a pas de devis associé
        $this->addFlash('error', 'La facture n\'a pas de devis associé.');

        // Redirigez vers une page appropriée
        return $this->redirectToRoute('app_facture_index');
    }
}
