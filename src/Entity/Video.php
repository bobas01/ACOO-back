<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\VideoRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use App\Controller\VideoController;
use Symfony\Component\Serializer\Attribute\Groups;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\GetCollection;

#[ORM\Entity(repositoryClass: VideoRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/videos',
            controller: VideoController::class . '::getAll',
            normalizationContext: ['groups' => ['video:read']]
        ),
        new Get(
            uriTemplate: '/videos/{id}',
            controller: VideoController::class . '::getOne',
            normalizationContext: ['groups' => ['video:read']]
        ),
        new Post(
            uriTemplate: '/videos',
            controller: VideoController::class . '::create',
            deserialize: false,
            denormalizationContext: ['groups' => ['video:write']]
        ),
        new Post(
            uriTemplate: '/videos/{id}',
            controller: VideoController::class . '::update',
            deserialize: false,
            denormalizationContext: ['groups' => ['video:write']]
        ),
        new Delete(
            uriTemplate: '/videos/{id}',
            controller: VideoController::class . '::delete',
        )
    ],
    formats: ['json', 'multipart' => ['multipart/form-data']]
)]
class Video
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['video:read', 'video:write'])]
    #[ApiProperty(description: 'Identifiant unique de la vidéo')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['video:read', 'video:write'])]
    #[ApiProperty(description: 'Nom ou titre de la vidéo')]
    private string $name;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    #[Groups(['video:read', 'video:write'])]
    #[ApiProperty(description: 'Indique si la vidéo est sélectionnée pour le hero', example: false)]
    private bool $highlighting = false;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['video:read', 'video:write'])]
    #[ApiProperty(description: 'URL de la vidéo')]
    private string $videoUrl;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function isHighlighting(): bool
    {
        return $this->highlighting;
    }

    public function setHighlighting(bool $highlighting): self
    {
        $this->highlighting = $highlighting;
        return $this;
    }

    public function getVideoUrl(): string
    {
        return $this->videoUrl;
    }

    public function setVideoUrl(string $videoUrl): self
    {
        $this->videoUrl = $videoUrl;
        return $this;
    }
} 