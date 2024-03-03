<?php

namespace App\Controller\Front;

use App\Entity\Category;
use App\Entity\Devis;
use App\Form\DevisType;
use App\Repository\DevisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Entity\Facture;

#[Route('/devis')]
class DevisController extends AbstractController
{
    #[Route('/', name: 'app_devis_index', methods: ['GET'])]
    public function index(DevisRepository $devisRepository): Response
    {
        return $this->render('front/devis/index.html.twig', [
            'devis' => $devisRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_devis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        //$entrepriseId = 1;
        $products = $entityManager->getRepository(Product::class)->findAll();

        $productArray = [];
        foreach ($products as $product) {
            $category = $product->getCategory();
            $productArray[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice(),
                'tva' => $product->getTva(),
                'category' => $category->getName(),
                // Ajoutez d'autres champs selon votre entité
            ];
        }

        $products = json_encode($productArray);

        $devis = new Devis();
        $devis->setNumDevis('D' . date('Ymd') . '-' . rand(1000, 9999));
        $form = $this->createForm(DevisType::class, $devis);
        /*$form = $this->createForm(DevisType::class, $devi, [
            'entreprise_id' => $entrepriseId,
        ]);*/
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Recupere tout les produits
            $products = $devis->getProduits();
            //Recupere tout les attribut de mon objet pour les mettre dans un tableau
            // On effectue ce traitement car sinon, les element n'arrive pas a etre enregistrer dans la base de données
            $prod = [];
            foreach ($products as $product) {
                $product->setName($productArray[$product->getName()]["name"]);
                $prodtemp = $product->jsonSerialize();
                $prodtemp['category'] = $product->getCategory()->jsonSerialize();

                $prod[] = $prodtemp;
            }
            // on enregistre les produits dans l'objet devis
            $devis->setProduits($prod);
            // calcul du prix total
            $totalPrice = 0;
            foreach ($products as $product) {
                $totalPrice += $product->getPrixTotale();
            }

            $devis->setTotalPrice($totalPrice);

            $entityManager->persist($devis);
            $entityManager->flush();

            return $this->redirectToRoute('front_app_devis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('front/devis/new.html.twig', [
            'devis' => $devis,
            'form' => $form,
            'product' => $products,
        ]);
    }

    #[Route('/{id}', name: 'app_devis_show', methods: ['GET'])]
    public function show(Devis $devis): Response
    {
        dump($devis);
        return $this->render('front/devis/show.html.twig', [
            'devis' => $devis,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_devis_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Devis $devis, EntityManagerInterface $entityManager): Response
    {

        $products = $entityManager->getRepository(Product::class)->findAll();

        $productArray = [];
        foreach ($products as $product) {
            $category = $product->getCategory();
            $productArray[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice(),
                'tva' => $product->getTva(),
                'category' => $category->getName(),
                // Ajoutez d'autres champs selon votre entité
            ];
        }

        $products = json_encode($productArray);

        dump($devis->getProduits()[0]);
        $prodTemp = [];
        foreach ($devis->getProduits() as $product) {
            $produitTemp = Product::arrayToProduit($product);
            $categoryTemp = $entityManager->getRepository(Category::class)->findOneBy(['id' => $product['category']['id']]);
            $produitTemp->setCategory($categoryTemp);
            $prodTemp[] = $produitTemp;
        }
        $devis->setProduits($prodTemp);

        $form = $this->createForm(DevisType::class, $devis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {



            // Recupere tout les produits
            $products = $devis->getProduits();
            //Recupere tout les attribut de mon objet pour les mettre dans un tableau
            // On effectue ce traitement car sinon, les element n'arrive pas a etre enregistrer dans la base de données
            $prod = [];
            foreach ($products as $product) {
                $product->setName($productArray[$product->getName()]["name"]);
                $prodtemp = $product->jsonSerialize();
                $prodtemp['category'] = $product->getCategory()->jsonSerialize();

                $prod[] = $prodtemp;
            }
            // on enregistre les produits dans l'objet devis
            $devis->setProduits($prod);
            // calcul du prix total
            $totalPrice = 0;
            foreach ($products as $product) {
                $totalPrice += $product->getPrixTotale();
            }

            $devis->setTotalPrice($totalPrice);
            $entityManager->flush();

            return $this->redirectToRoute('front_app_devis_index', [], Response::HTTP_SEE_OTHER);
        }

        $produitsTable = [];
        $produitDevis = $devis->getProduits();
        foreach ($produitDevis as $produit) {
            $categoryTemp = $produit->getCategory()->jsonSerialize();
            $produitsTemp = $produit->jsonSerialize();
            $produitsTemp['category'] = $categoryTemp;
            $produitsTable[] = $produitsTemp;
        }

        return $this->render('front/devis/edit.html.twig', [
            'devis' => $devis,
            'devisProduit' => json_encode($produitsTable),
            'form' => $form,
            'product' => $products,
        ]);
    }

    #[Route('/{id}', name: 'app_devis_delete', methods: ['POST'])]
    public function delete(Request $request, Devis $devi, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$devi->getId(), $request->request->get('_token'))) {
            $entityManager->remove($devi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('front_app_devis_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/transform-devis', name: 'app_devis_transform_devis', methods: ['GET', 'POST'])]
    public function transform_devis(Request $request, Devis $devis, EntityManagerInterface $entityManager): Response
    {

        $facture = new Facture();
        $facture->setNumFacture('F' . date('Ymd') . '-' . rand(1000, 9999));
        $facture->setClient($devis->getClient());
        $facture->setDateFacture(new \DateTime());
        $facture->setNumDevis($devis->getNumDevis());
        $facture->setPrixTotal($devis->getTotalPrice());
        $facture->setProduits($devis->getProduits());
        //$facture->setCompagny($devi->getEntreprise());
        $facture->setPaid(false);
        $facture->setPrixPaye(0);
        $facture->setReduction(0);
        $dateEcheance = new \DateTime();
        $dateEcheance = $dateEcheance->modify('+1 month');
        $facture->setDateEcheance($dateEcheance);

        $entityManager->persist($facture);
        $entityManager->flush();

        return $this->redirectToRoute('front_app_facture_index', [], Response::HTTP_SEE_OTHER);

    }
}
