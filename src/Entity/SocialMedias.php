<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use App\Controller\SocialMediasController;
use App\Repository\SocialMediasRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SocialMediasRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/social-media/{id}',
            controller: SocialMediasController::class . '::show',
            normalizationContext: ['groups' => ['social_media:read']]
        ),
        new GetCollection(
            uriTemplate: '/social-media',
            controller: SocialMediasController::class . '::index',
            normalizationContext: ['groups' => ['social_media:read']]
        ),
        new Post(
            uriTemplate: '/social-media',
            controller: SocialMediasController::class . '::create',
            deserialize: false,
            denormalizationContext: ['groups' => ['social_media:write']]
        ),
        new Post(
            uriTemplate: '/social-media/{id}',
            controller: SocialMediasController::class . '::update',
            deserialize: false,
            denormalizationContext: ['groups' => ['social_media:write']]
        ),
        new Delete(
            uriTemplate: '/social-media/{id}',
            controller: SocialMediasController::class . '::delete'
        )
    ],
    formats: ['json', 'multipart' => ['multipart/form-data']]
)]
class SocialMedias
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['social_media:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['social_media:read', 'social_media:write'])]
    private ?string $platform = null;

    #[ORM\Column(length: 255)]
    #[Groups(['social_media:read', 'social_media:write'])]
    private ?string $url = null;

    #[ORM\Column(length: 255)]
    #[Groups(['social_media:read', 'social_media:write'])]
    private ?string $iconUrl = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlatform(): ?string
    {
        return $this->platform;
    }

    public function setPlatform(string $platform): static
    {
        $this->platform = $platform;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;
        return $this;
    }

    public function getIconUrl(): ?string
    {
        return $this->iconUrl;
    }

    public function setIconUrl(string $iconUrl): static
    {
        $this->iconUrl = $iconUrl;
        return $this;
    }
}
