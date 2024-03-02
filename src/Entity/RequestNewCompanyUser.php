<?php

namespace App\Entity;

use App\Repository\RequestNewCompanyUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: RequestNewCompanyUserRepository::class)]
class RequestNewCompanyUser
{
    use Traits\Timestampable;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $CompanyId = null;

    #[ORM\Column(length: 50)]
    private ?string $role = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $UserId = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getCompanyId(): ?Uuid
    {
        return $this->CompanyId;
    }

    public function setCompanyId(Uuid $CompanyId): static
    {
        $this->CompanyId = $CompanyId;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

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

    public function getUserId(): ?Uuid
    {
        return $this->UserId;
    }

    public function setUserId(Uuid $UserId): static
    {
        $this->UserId = $UserId;

        return $this;
    }
}
