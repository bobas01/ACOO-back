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
use ApiPlatform\Metadata\ApiProperty;

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
    #[ApiProperty(description: 'Identifiant unique du réseau social')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['social_media:read', 'social_media:write'])]
    #[ApiProperty(
        description: 'Nom du réseau social',
        example: 'Facebook',
        required: true
    )]
    private ?string $platform = null;

    #[ORM\Column(length: 255)]
    #[Groups(['social_media:read', 'social_media:write'])]
    #[ApiProperty(
        description: 'URL du profil',
        example: 'https://facebook.com/notre-association',
        required: true
    )]
    private ?string $url = null;

    #[ORM\Column(length: 255, options: ['default' => 'https://cdn-icons-png.flaticon.com/512/7046/7046086.png'])]
    #[Groups(['social_media:read', 'social_media:write'])]
    #[ApiProperty(
        description: 'Logo du réseau social',
        example: ['data:image/jpeg;base64,...'],
               required: false,
    default: 'https://cdn-icons-png.flaticon.com/512/7046/7046086.png'
    )]
    private ?string $iconUrl = null;

    #[ORM\OneToMany(targetEntity: Images::class, mappedBy: 'socialMedias')]
    #[Groups(['social_media:read', 'social_media:write'])]
    #[ApiProperty(
        description: 'Logo du réseau social',
        example: ['data:image/jpeg;base64,...']
    )]
    private Collection $image;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['social_media:read'])]
    #[ApiProperty(description: 'Date de création du réseau social')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['social_media:read'])]
    #[ApiProperty(description: 'Date de dernière mise à jour du réseau social')]
    private ?\DateTimeImmutable $updatedAt = null;

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
