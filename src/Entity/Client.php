<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    use Traits\Timestampable;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Nom = null;

    #[ORM\Column(length: 255)]
    private ?string $Prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $Email = null;

    #[ORM\Column(length: 255)]
    private ?string $NumeroTelephone = null;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Devis::class)]
    private Collection $devis;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Facture::class)]
    private Collection $factures;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address_number = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address_type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address_zip_code = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address_city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address_country = null;

    public function __construct()
    {
        $this->devis = new ArrayCollection();
        $this->factures = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getNom();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): static
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->Prenom;
    }

    public function setPrenom(string $Prenom): static
    {
        $this->Prenom = $Prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(string $Email): static
    {
        $this->Email = $Email;

        return $this;
    }

    public function getNumeroTelephone(): ?string
    {
        return $this->NumeroTelephone;
    }

    public function setNumeroTelephone(string $NumeroTelephone): static
    {
        $this->NumeroTelephone = $NumeroTelephone;

        return $this;
    }

    public function getDevis(): Collection
    {
        return $this->devis;
    }

    public function setDevis(Collection $devis): self
    {
        $this->devis = $devis;

        foreach ($devis as $devi) {
            $devi->setClient($this);
        }

        return $this;
    }

    public function setFactures(Collection $factures): self
    {
        $this->factures = $factures;

        foreach ($factures as $facture) {
            $facture->setClient($this);
        }

        return $this;
    }

    public function getFactures(): Collection
    {
        return $this->factures;
    }

    public function getAddressNumber(): ?string
    {
        return $this->address_number;
    }

    public function setAddressNumber(?string $address_number): static
    {
        $this->address_number = $address_number;

        return $this;
    }

    public function getAddressType(): ?string
    {
        return $this->address_type;
    }

    public function setAddressType(?string $address_type): static
    {
        $this->address_type = $address_type;

        return $this;
    }

    public function getAddressName(): ?string
    {
        return $this->address_name;
    }

    public function setAddressName(?string $address_name): static
    {
        $this->address_name = $address_name;

        return $this;
    }

    public function getAddressZipCode(): ?string
    {
        return $this->address_zip_code;
    }

    public function setAddressZipCode(?string $address_zip_code): static
    {
        $this->address_zip_code = $address_zip_code;

        return $this;
    }

    public function getAddressCity(): ?string
    {
        return $this->address_city;
    }

    public function setAddressCity(?string $address_city): static
    {
        $this->address_city = $address_city;

        return $this;
    }

    public function getAddressCountry(): ?string
    {
        return $this->address_country;
    }

    public function setAddressCountry(?string $address_country): static
    {
        $this->address_country = $address_country;

        return $this;
    }

    public function getFullAddress(): string
    {
        return $this->address_number . ' ' . $this->address_type . ' ' . $this->address_name . ' ' . $this->address_zip_code . ' ' . $this->address_city . ' ' . $this->address_country;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars( $this );
    }

    public static function arrayToClient($array)
    {
        // Vérifier que le tableau contient les clés nécessaires

        $client = new Client();
        $client->setNom($array['Nom']);
        $client->setPrenom($array['Prenom']);
        $client->setEmail($array['Email']);
        $client->setNumeroTelephone($array['NumeroTelephone']);
        $client->setAddressNumber($array['address_number']);
        $client->setAddressType($array['address_type']);
        $client->setAddressName($array['address_name']);
        $client->setAddressZipCode($array['address_zip_code']);
        $client->setAddressCity($array['address_city']);
        $client->setAddressCountry($array['address_country']);
        return $client;
    }
}
