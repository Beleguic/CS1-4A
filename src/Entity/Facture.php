<?php

namespace App\Entity;

use App\Repository\FactureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: FactureRepository::class)]
class Facture
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $num_facture = null;

    #[ORM\Column(length: 255)]
    private ?string $num_devis = null;

    #[ORM\ManyToOne(inversedBy: 'client')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;


    #[ORM\Column]
    private ?bool $paid = null;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Gedmo\Timestampable(on: 'create')]
    private \DateTime $date_facture;

    #[ORM\Column(type: 'uuid', nullable: true)]
    private ?Uuid $company = null;

    #[ORM\Column]
    private ?float $prix_total = null;

    #[ORM\Column]
    private ?float $prix_paye = null;

    #[ORM\Column(nullable: true)]
    private ?int $reduction = null;

    #[ORM\Column]
    private array $produits = [];

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_echeance = null;




    public function getId(): Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getNumFacture(): ?string
    {
        return $this->num_facture;
    }

    public function setNumFacture(string $num_facture): static
    {
        $this->num_facture = $num_facture;

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

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function isPaid(): ?bool
    {
        return $this->paid;
    }

    public function setPaid(bool $paid): static
    {
        $this->paid = $paid;

        return $this;
    }

    public function getDateFacture(): ?\DateTime
    {
        return $this->date_facture;
    }

    public function setDateFacture(\DateTime $date_facture): static
    {
        $this->date_facture = $date_facture;

        return $this;
    }

    public function getCompany(): ?Uuid
    {
        return $this->company;
    }

    public function setCompany(Uuid $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getPrixTotal(): ?float
    {
        return $this->prix_total;
    }

    public function setPrixTotal(float $prix_total): static
    {
        $this->prix_total = $prix_total;

        return $this;
    }

    public function getPrixPaye(): ?float
    {
        return $this->prix_paye;
    }

    public function setPrixPaye(float $prix_paye): static
    {
        $this->prix_paye = $prix_paye;

        return $this;
    }

    public function getReduction(): ?int
    {
        return $this->reduction;
    }

    public function setReduction(?int $reduction): static
    {
        $this->reduction = $reduction;

        return $this;
    }

    public function getProduits(): array
    {
        return $this->produits;
    }

    public function setProduits(array $produits): static
    {
        $this->produits = $produits;

        return $this;
    }

    public function getDateEcheance(): ?\DateTimeInterface
    {
        return $this->date_echeance;
    }

    public function setDateEcheance(?\DateTimeInterface $date_echeance): static
    {
        $this->date_echeance = $date_echeance;

        return $this;
    }


}