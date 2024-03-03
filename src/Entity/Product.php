<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Category;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    use Traits\Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column]
    private ?float $tva = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\Column(nullable: true)]
    private ?int $quantite = null;

    #[ORM\Column(nullable: true)]
    private ?float $prix_totale = null;


    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->name = $id;

        return $this;
    }


    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getTva(): ?float
    {
        return $this->tva;
    }

    public function setTva(float $tva): static
    {
        $this->tva = $tva;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getPrixTotale(): ?float
    {
        return $this->prix_totale;
    }

    public function setPrixTotale(?float $prix_totale): static
    {
        $this->prix_totale = $prix_totale;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars( $this );
    }

    public static function arrayToProduit($array)
    {
        // Vérifier que le tableau contient les clés nécessaires


        $produit = new Product();
        $produit->setName($array['name']);
        $produit->setDescription($array['description']);
        $produit->setPrice($array['price']);
        $produit->setTva($array['tva']);
        $produit->setQuantite($array['quantite']);
        $produit->setPrixTotale($array['prix_totale']);
        return $produit;
    }


}
