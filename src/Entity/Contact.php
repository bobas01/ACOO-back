<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use App\Controller\ContactController;
use App\Repository\ContactRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiProperty;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/contact/{id}',
            controller: ContactController::class . '::show',
            normalizationContext: ['groups' => ['contact:read']]
        ),
        new GetCollection(
            uriTemplate: '/contact',
            controller: ContactController::class . '::index',
            normalizationContext: ['groups' => ['contact:read']]
        ),
        new Post(
            uriTemplate: '/contact',
            controller: ContactController::class . '::create',
            denormalizationContext: ['groups' => ['contact:write']]
        ),
        new Delete(
            uriTemplate: '/contact/{id}',
            controller: ContactController::class . '::delete'
        )
    ]
)]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['contact:read'])]
    #[ApiProperty(description: 'Identifiant unique du message de contact')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['contact:read', 'contact:write'])]
    #[ApiProperty(
        description: 'Nom de l\'expéditeur',
        example: 'John Doe',
        required: true
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['contact:read', 'contact:write'])]
    #[ApiProperty(
        description: 'Adresse email de l\'expéditeur',
        example: 'john.doe@example.com',
        required: true
    )]
    private ?string $mail = null;

    #[ORM\Column(length: 255)]
    #[Groups(['contact:read', 'contact:write'])]
    #[ApiProperty(
        description: 'Sujet du message',
        example: 'Demande d\'information',
        required: true
    )]
    private ?string $subject = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['contact:read', 'contact:write'])]
    #[ApiProperty(
        description: 'Contenu du message',
        example: 'Je souhaiterais obtenir des informations sur...',
        required: true
    )]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['contact:read'])]
    #[ApiProperty(description: 'Date d\'envoi du message')]
    private ?\DateTimeImmutable $created_at = null;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
    }

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

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;
        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;
        return $this;
    }
}
