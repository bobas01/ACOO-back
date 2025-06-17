<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use App\Repository\ContactClubRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ContactClubRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['contact_club:read']],
    denormalizationContext: ['groups' => ['contact_club:write']],
    formats: ['json', 'multipart' => ['multipart/form-data']]
)]
class ContactClub
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['contact_club:read'])]
    #[ApiProperty(description: 'Identifiant unique du contact')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['contact_club:read', 'contact_club:write'])]
    #[ApiProperty(
        description: 'Nom du contact',
        example: 'John Doe',
        required: true
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['contact_club:read', 'contact_club:write'])]
    #[ApiProperty(
        description: 'Adresse email du contact',
        example: 'contact@example.com',
        required: true
    )]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(['contact_club:read', 'contact_club:write'])]
    #[ApiProperty(
        description: 'Numéro de téléphone du contact',
        example: '+33123456789',
        required: true
    )]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    #[Groups(['contact_club:read', 'contact_club:write'])]
    #[ApiProperty(
        description: 'Fonction du contact',
        example: 'Président',
        required: true
    )]
    private ?string $function = null;

    #[ORM\OneToMany(targetEntity: Images::class, mappedBy: 'contactClub')]
    #[Groups(['contact_club:read', 'contact_club:write'])]
    #[ApiProperty(
        description: 'Photo du contact',
        example: ['data:image/jpeg;base64,...']
    )]
    private Collection $image;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['contact_club:read'])]
    #[ApiProperty(description: 'Date de création du contact')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['contact_club:read'])]
    #[ApiProperty(description: 'Date de dernière mise à jour du contact')]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getFunction(): ?string
    {
        return $this->function;
    }

    public function setFunction(string $function): static
    {
        $this->function = $function;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
