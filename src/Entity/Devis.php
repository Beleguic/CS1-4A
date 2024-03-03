<?php

namespace App\Entity;

use App\Repository\DevisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: DevisRepository::class)]
class Devis
{

    use Traits\Timestampable;
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'client')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    // La variable Produits va contenir des objet produit


    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\Column(length: 255)]
    private ?string $num_devis = null;
    #[ORM\Column]
    private ?float $total_price = null;

    #[ORM\Column(nullable: true)]
    private ?array $produits = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $company_id = null;



    public function __construct()
    {
        // $this->produits = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }



    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getNumDevis(): ?string
    {
        return $this->num_devis;
    }

    public function setNumDevis(string $num_devis): static
    {
        $this->num_devis = $num_devis;

        return $this;
    }


    public function getTotalPrice(): ?float
    {
        return $this->total_price;
    }

    public function setTotalPrice(float $total_price): static
    {
        $this->total_price = $total_price;

        return $this;
    }

    public function getProduits(): ?array
    {
        return $this->produits;
    }

    public function setProduits(?array $produits): static
    {
        $this->produits = $produits;

        return $this;
    }

    public function getCompanyId(): ?Uuid
    {
        return $this->company_id;
    }

    public function setCompanyId(Uuid $company_id): static
    {
        $this->company_id = $company_id;

        return $this;
    }




}
