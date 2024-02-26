<?php

namespace App\Controller\Front;

use App\Entity\Contact;
use App\Form\Front\ContactType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/contact-us')]
class ContactController extends AbstractController
{

    #[Route('/', name: 'app_contact', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($contact);
            $entityManager->flush();
            $this->addFlash('success', 'Thank you to contact us! We will contact you soon');

            return $this->redirectToRoute('front_app_contact', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('front/contact/new.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }
}
