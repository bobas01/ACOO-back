<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use App\Controller\GalleryController;
use App\Repository\GalleryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiProperty;

#[ORM\Entity(repositoryClass: GalleryRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/gallery/{id}',
            controller: GalleryController::class . '::show',
            normalizationContext: ['groups' => ['gallery:read']]
        ),
        new GetCollection(
            uriTemplate: '/gallery',
            controller: GalleryController::class . '::index',
            normalizationContext: ['groups' => ['gallery:read']]
        ),
        new Post(
            uriTemplate: '/gallery',
            controller: GalleryController::class . '::create',
            denormalizationContext: ['groups' => ['gallery:write']]
        ),
        new Post(
            uriTemplate: '/gallery/{id}',
            controller: GalleryController::class . '::update',
            denormalizationContext: ['groups' => ['gallery:write']]
        ),
        new Delete(
            uriTemplate: '/gallery/{id}',
            controller: GalleryController::class . '::delete'
        )
    ],
    formats: ['json']
)]
class Gallery
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['gallery:read'])]
    #[ApiProperty(description: 'Identifiant unique de la galerie')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['gallery:read', 'gallery:write'])]
    #[ApiProperty(
        description: 'Thème de la galerie',
        example: 'Championnats de France 2024',
        required: true
    )]
    private ?string $theme = null;

    /**
     * @var Collection<int, Pictures>
     */
    #[ORM\OneToMany(targetEntity: Pictures::class, mappedBy: 'id_gallery', cascade: ['persist', 'remove'])]
    #[Groups(['gallery:read'])]
    #[ApiProperty(
        description: 'Liste des photos dans la galerie',
        example: [
            [
                'id' => 1,
                'description' => 'Photo de la finale',
                'image' => 'http://localhost:8000/uploads/images/pictures/image-123456789.jpg'
            ]
        ]
    )]
    private Collection $pictures;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['gallery:read'])]
    #[ApiProperty(description: 'Date de création de la galerie')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['gallery:read'])]
    #[ApiProperty(description: 'Date de dernière mise à jour de la galerie')]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(string $theme): static
    {
        $this->theme = $theme;
        return $this;
    }

    /**
     * @return Collection<int, Pictures>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Pictures $picture): static
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures->add($picture);
            $picture->setIdGallery($this);
        }
        return $this;
    }

    public function removePicture(Pictures $picture): static
    {
        if ($this->pictures->removeElement($picture)) {
            if ($picture->getIdGallery() === $this) {
                $picture->setIdGallery(null);
            }
        }
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
