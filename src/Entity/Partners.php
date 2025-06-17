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
use ApiPlatform\Metadata\ApiProperty;

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
    #[ApiProperty(description: 'Identifiant unique du partenaire')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['partners:read', 'partners:write'])]
    #[ApiProperty(
        description: 'Nom du partenaire',
        example: 'Nike',
        required: true
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['partners:read', 'partners:write'])]
    #[ApiProperty(
        description: 'URL du site web du partenaire',
        example: 'https://www.nike.com',
        required: true
    )]
    private ?string $url = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['partners:read', 'partners:write'])]
    #[ApiProperty(
        description: 'Description détaillée du partenaire',
        example: 'Équipementier sportif officiel',
        required: true
    )]
    private ?string $description = null;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['partners:read', 'partners:write'])]
    #[ApiProperty(
        description: 'Indique si le partenaire est un sponsor',
        example: true,
        default: false
    )]
    private ?bool $sponsor = false;

    /**
     * @var Collection<int, Images>
     */
    #[ORM\OneToMany(targetEntity: Images::class, mappedBy: 'partners', cascade: ['persist', 'remove'])]
    #[Groups(['partners:read'])]
    #[ApiProperty(
        description: 'Images associées au partenaire',
        example: ['data:image/jpeg;base64,...']
    )]
    private Collection $image;

    #[ORM\OneToMany(targetEntity: Images::class, mappedBy: 'partners')]
    #[Groups(['partners:read', 'partners:write'])]
    #[ApiProperty(
        description: 'Logo du partenaire',
        example: ['data:image/jpeg;base64,...']
    )]
    private Collection $logo;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['partners:read'])]
    #[ApiProperty(description: 'Date de création du partenaire')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['partners:read'])]
    #[ApiProperty(description: 'Date de dernière mise à jour du partenaire')]
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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;
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

    /**
     * @return Collection<int, Images>
     */
    public function getLogo(): Collection
    {
        return $this->logo;
    }

    public function addLogo(Images $logo): static
    {
        if (!$this->logo->contains($logo)) {
            $this->logo->add($logo);
            $logo->setPartners($this);
        }
        return $this;
    }

    public function removeLogo(Images $logo): static
    {
        if ($this->logo->removeElement($logo)) {
            if ($logo->getPartners() === $this) {
                $logo->setPartners(null);
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
