<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use App\Repository\ContactClubRepository;
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
        description: 'Numéro de téléphone du contact',
        example: '+33123456789',
        required: true
    )]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 255)]
    #[Groups(['contact_club:read', 'contact_club:write'])]
    #[ApiProperty(
        description: 'Adresse email du contact',
        example: 'contact@example.com',
        required: true
    )]
    private ?string $mail = null;

    #[ORM\Column(length: 255)]
    #[Groups(['contact_club:read', 'contact_club:write'])]
    #[ApiProperty(
        description: 'Adresse du contact',
        example: '12 rue de Paris, 75000 Paris',
        required: true
    )]
    private ?string $address = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }
}