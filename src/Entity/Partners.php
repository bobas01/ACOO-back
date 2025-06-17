<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use App\Controller\PartnersController;
use App\Repository\PartnersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PartnersRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/partners/{id}',
            controller: PartnersController::class . '::show',
            normalizationContext: ['groups' => ['partners:read']]
        ),
        new GetCollection(
            uriTemplate: '/partners',
            controller: PartnersController::class . '::index',
            normalizationContext: ['groups' => ['partners:read']]
        ),
        new Post(
            uriTemplate: '/partners',
            controller: PartnersController::class . '::create',
            deserialize: false,
            denormalizationContext: ['groups' => ['partners:write']]
        ),
        new Post(
            uriTemplate: '/partners/{id}',
            controller: PartnersController::class . '::update',
            deserialize: false,
            denormalizationContext: ['groups' => ['partners:write']]
        ),
        new Delete(
            uriTemplate: '/partners/{id}',
            controller: PartnersController::class . '::delete'
        )
    ],
    formats: ['json', 'multipart' => ['multipart/form-data']]
)]
class Partners
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['partners:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['partners:read', 'partners:write'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['partners:read', 'partners:write'])]
    private ?string $description = null;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['partners:read', 'partners:write'])]
    private ?bool $sponsor = false;

    /**
     * @var Collection<int, Images>
     */
    #[ORM\OneToMany(targetEntity: Images::class, mappedBy: 'partners', cascade: ['persist', 'remove'])]
    #[Groups(['partners:read'])]
    private Collection $image;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['partners:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['partners:read'])]
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
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

    public function isSponsor(): ?bool
    {
        return $this->sponsor;
    }

    public function setSponsor(bool $sponsor): static
    {
        $this->sponsor = $sponsor;
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
            $image->setPartners($this);
        }
        return $this;
    }

    public function removeImage(Images $image): static
    {
        if ($this->image->removeElement($image)) {
            if ($image->getPartners() === $this) {
                $image->setPartners(null);
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
