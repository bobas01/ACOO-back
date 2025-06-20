<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use App\Controller\PicturesController;
use App\Repository\PicturesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiProperty;
use App\Entity\Gallery;

#[ORM\Entity(repositoryClass: PicturesRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/pictures/{id}',
            controller: PicturesController::class . '::show',
            normalizationContext: ['groups' => ['pictures:read']]
        ),
        new GetCollection(
            uriTemplate: '/pictures',
            controller: PicturesController::class . '::index',
            normalizationContext: ['groups' => ['pictures:read']]
        ),
        new Post(
            uriTemplate: '/pictures',
            controller: PicturesController::class . '::create',
            deserialize: false,
            denormalizationContext: ['groups' => ['pictures:write']]
        ),
        new Post(
            uriTemplate: '/pictures/{id}',
            controller: PicturesController::class . '::update',
            deserialize: false,
            denormalizationContext: ['groups' => ['pictures:write']]
        ),
        new Delete(
            uriTemplate: '/pictures/{id}',
            controller: PicturesController::class . '::delete'
        )
    ],
    formats: ['json', 'multipart' => ['multipart/form-data']]
)]
class Pictures
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['pictures:read'])]
    #[ApiProperty(description: 'Identifiant unique de la photo')]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['pictures:read', 'pictures:write'])]
    #[ApiProperty(
        description: 'Description de la photo',
        example: 'Photo de la finale du 100m',
        required: true
    )]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'pictures')]
    #[Groups(['pictures:read', 'pictures:write'])]
    #[ApiProperty(
        description: 'Galerie à laquelle appartient la photo',
        example: 1,
        required: true
    )]
    private ?Gallery $id_gallery = null;

    /**
     * @var Collection<int, Images>
     */
    #[ORM\OneToMany(targetEntity: Images::class, mappedBy: 'pictures', cascade: ['persist', 'remove'])]
    #[Groups(['pictures:read', 'pictures:write'])]
    #[ApiProperty(
        description: 'Images associées à la photo (tableau base64 pour upload)',
        example: ['data:image/jpeg;base64,...'],
        required: false
    )]
    private Collection $images;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['pictures:read'])]
    #[ApiProperty(description: 'Date de création de la photo')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['pictures:read'])]
    #[ApiProperty(description: 'Date de dernière mise à jour de la photo')]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIdGallery(): ?Gallery
    {
        return $this->id_gallery;
    }

    public function setIdGallery(?Gallery $id_gallery): static
    {
        $this->id_gallery = $id_gallery;
        return $this;
    }

    /**
     * @return Collection<int, Images>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Images $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setPictures($this);
        }
        return $this;
    }

    public function removeImage(Images $image): static
    {
        if ($this->images->removeElement($image)) {
            if ($image->getPictures() === $this) {
                $image->setPictures(null);
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
