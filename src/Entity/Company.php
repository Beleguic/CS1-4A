<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[Vich\Uploadable]
class Company
{
    use Traits\Timestampable;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $invoiceEmail = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $addressNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $addressType = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $addressName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $addressZipCode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $addressCity = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $addressCountry = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $companyNumber = null;

    #[ORM\Column(length: 34, nullable: true)]
    private ?string $iban = null;

    #[ORM\Column(length: 11, nullable: true)]
    private ?string $bic = null;

    #[Vich\UploadableField(mapping: 'companyLogo', fileNameProperty: 'imageName', size: 'imageSize')]
    #[Assert\Image(
        maxSize: '1000k',
        mimeTypes: ['image/jpeg', 'image/png'],
        maxRatio: '1',
        minRatio: '1',
        maxSizeMessage: 'The file must not be larger than {{ limit }}Mo, but it is {{ size }}',
        mimeTypesMessage: 'The file must be in JPG or PNG format',
        maxRatioMessage: 'The file ratio is too big {{ ratio }}. Ratio must be 1 (square)',
        minRatioMessage : 'The file ratio is too small {{ ratio }}. Ratio must be 1 (square)',
    )]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?int $imageSize = null;

    public function getId(): ?Uuid
    {
        return $this->id;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getInvoiceEmail(): ?string
    {
        return $this->invoiceEmail;
    }

    public function setInvoiceEmail(string $invoiceEmail): static
    {
        $this->invoiceEmail = $invoiceEmail;

        return $this;
    }

    public function getAddressNumber(): ?string
    {
        return $this->addressNumber;
    }

    public function setAddressNumber(string $addressNumber): static
    {
        $this->addressNumber = $addressNumber;

        return $this;
    }

    public function getAddressType(): ?string
    {
        return $this->addressType;
    }

    public function setAddressType(string $addressType): static
    {
        $this->addressType = $addressType;

        return $this;
    }

    public function getAddressName(): ?string
    {
        return $this->addressName;
    }

    public function setAddressName(string $addressName): static
    {
        $this->addressName = $addressName;

        return $this;
    }

    public function getAddressZipCode(): ?string
    {
        return $this->addressZipCode;
    }

    public function setAddressZipCode(string $addressZipCode): static
    {
        $this->addressZipCode = $addressZipCode;

        return $this;
    }

    public function getAddressCity(): ?string
    {
        return $this->addressCity;
    }

    public function setAddressCity(string $addressCity): static
    {
        $this->addressCity = $addressCity;

        return $this;
    }

    public function getAddressCountry(): ?string
    {
        return $this->addressCountry;
    }

    public function setAddressCountry(string $addressCountry): static
    {
        $this->addressCountry = $addressCountry;

        return $this;
    }

    public function getCompanyNumber(): ?string
    {
        return $this->companyNumber;
    }

    public function setCompanyNumber(string $companyNumber): static
    {
        $this->companyNumber = $companyNumber;

        return $this;
    }

    public function getIban(): ?string
    {
        return $this->iban;
    }

    public function setIban(string $iban): static
    {
        $this->iban = $iban;

        return $this;
    }

    public function getBic(): ?string
    {
        return $this->bic;
    }

    public function setBic(string $bic): static
    {
        $this->bic = $bic;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getID();
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTime();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }
}
