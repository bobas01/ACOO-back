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
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['pictures:read', 'pictures:write'])]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'pictures')]
    #[Groups(['pictures:read', 'pictures:write'])]
    private ?Gallery $id_gallery = null;

    /**
     * @var Collection<int, Images>
     */
    #[ORM\OneToMany(targetEntity: Images::class, mappedBy: 'pictures', cascade: ['persist', 'remove'])]
    #[Groups(['pictures:read'])]
    private Collection $image;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['pictures:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['pictures:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->image = new ArrayCollection();
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
    public function getImage(): Collection
    {
        return $this->image;
    }

    public function addImage(Images $image): static
    {
        if (!$this->image->contains($image)) {
            $this->image->add($image);
            $image->setPictures($this);
        }
        return $this;
    }

    public function removeImage(Images $image): static
    {
        if ($this->image->removeElement($image)) {
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
