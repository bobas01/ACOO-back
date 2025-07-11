<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use App\Controller\PrizeListController;
use App\Repository\PrizeListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiProperty;

#[ORM\Entity(repositoryClass: PrizeListRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/prize-list/{id}',
            controller: PrizeListController::class . '::show',
            normalizationContext: ['groups' => ['prize_list:read']]
        ),
        new GetCollection(
            uriTemplate: '/prize-list',
            controller: PrizeListController::class . '::index',
            normalizationContext: ['groups' => ['prize_list:read']]
        ),
        new Post(
            uriTemplate: '/prize-list',
            controller: PrizeListController::class . '::create',
            deserialize: false,
            denormalizationContext: ['groups' => ['prize_list:write']]
        ),
        new Post(
            uriTemplate: '/prize-list/{id}',
            controller: PrizeListController::class . '::update',
            deserialize: false,
            denormalizationContext: ['groups' => ['prize_list:write']]
        ),
        new Delete(
            uriTemplate: '/prize-list/{id}',
            controller: PrizeListController::class . '::delete'
        )
    ],
    formats: ['json', 'multipart' => ['multipart/form-data']]
)]
class PrizeList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['prize_list:read'])]
    #[ApiProperty(description: 'Identifiant unique du palmarès')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['prize_list:read', 'prize_list:write'])]
    #[ApiProperty(
        description: 'Nom de l\'athlète',
        example: 'John Doe',
        required: true
    )]
    private ?string $athleteName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['prize_list:read', 'prize_list:write'])]
    #[ApiProperty(
        description: 'Nom de la compétition',
        example: 'Championnat de France',
        required: true
    )]
    private ?string $competition = null;

    #[ORM\Column(length: 255)]
    #[Groups(['prize_list:read', 'prize_list:write'])]
    #[ApiProperty(
        description: 'Catégorie de l\'athlète',
        example: 'Senior',
        required: true
    )]
    private ?string $category = null;

    #[ORM\Column(length: 255)]
    #[Groups(['prize_list:read', 'prize_list:write'])]
    #[ApiProperty(
        description: 'Sport pratiqué',
        example: 'Athlétisme',
        required: true
    )]
    private ?string $sport = null;

    #[ORM\Column(length: 255)]
    #[Groups(['prize_list:read', 'prize_list:write'])]
    #[ApiProperty(
        description: 'Genre de l\'athlète',
        example: 'Masculin',
        required: true
    )]
    private ?string $gender = null;

    #[ORM\Column(length: 255)]
    #[Groups(['prize_list:read', 'prize_list:write'])]
    #[ApiProperty(
        description: 'Résultat obtenu',
        example: '1er',
        required: true
    )]
    private ?string $result = null;

    #[ORM\Column]
    #[Groups(['prize_list:read', 'prize_list:write'])]
    #[ApiProperty(
        description: 'Année de la compétition',
        example: 2024,
        required: true
    )]
    private ?int $year = null;

    /**
     * @var Collection<int, Images>
     */
    #[ORM\OneToMany(targetEntity: Images::class, mappedBy: 'prizeList', cascade: ['persist', 'remove'])]
    #[ApiProperty(readable: false, writable: false)]
    private Collection $image;

    #[ApiProperty(
        description: 'Images associées au palmarès (tableau base64 pour upload)',
        example: ['data:image/jpeg;base64,...'],
        required: false
    )]
    #[Groups(['prize_list:read', 'prize_list:write'])]
    private ?array $images = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['prize_list:read'])]
    #[ApiProperty(description: 'Date de création du palmarès')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['prize_list:read'])]
    #[ApiProperty(description: 'Date de dernière mise à jour du palmarès')]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->image = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAthleteName(): ?string
    {
        return $this->athleteName;
    }

    public function setAthleteName(string $athleteName): static
    {
        $this->athleteName = $athleteName;

        return $this;
    }

    public function getCompetition(): ?string
    {
        return $this->competition;
    }

    public function setCompetition(string $competition): static
    {
        $this->competition = $competition;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getSport(): ?string
    {
        return $this->sport;
    }

    public function setSport(string $sport): static
    {
        $this->sport = $sport;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getResult(): ?string
    {
        return $this->result;
    }

    public function setResult(string $result): static
    {
        $this->result = $result;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;

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
            $image->setPrizeList($this);
        }

        return $this;
    }

    public function removeImage(Images $image): static
    {
        if ($this->image->removeElement($image)) {
            
            if ($image->getPrizeList() === $this) {
                $image->setPrizeList(null);
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

    public function getImages(): ?array
    {
        return $this->images;
    }

    public function setImages(?array $images): static
    {
        $this->images = $images;
        return $this;
    }
}
