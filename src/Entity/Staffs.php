<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\StaffsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Controller\StaffsController;

#[ORM\Entity(repositoryClass: StaffsRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/staffs/{id}',
            controller: StaffsController::class . '::show',
            normalizationContext: ['groups' => ['staffs:read']]
        ),
        new GetCollection(
            uriTemplate: '/staffs',
            controller: StaffsController::class . '::index',
            normalizationContext: ['groups' => ['staffs:read']]
        ),
        new Post(
            uriTemplate: '/staffs',
            controller: StaffsController::class . '::create',
            deserialize: false,
            denormalizationContext: ['groups' => ['staffs:write']]
        ),
        new Post(
            uriTemplate: '/staffs/{id}',
            controller: StaffsController::class . '::update',
            deserialize: false,
            denormalizationContext: ['groups' => ['staffs:write']]
        ),
        new Delete(
            uriTemplate: '/staffs/{id}',
            controller: StaffsController::class . '::delete'
        )
    ],
    formats: ['json', 'multipart' => ['multipart/form-data']]
)]
class Staffs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['staffs:read'])]
    #[ApiProperty(description: 'Identifiant unique du membre du staff')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['staffs:read', 'staffs:write'])]
    #[ApiProperty(
        description: 'Nom du membre du staff',
        example: 'John Doe',
        required: true
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['staffs:read', 'staffs:write'])]
    #[ApiProperty(
        description: 'Rôle du membre du staff',
        example: 'Entraîneur principal',
        required: false
    )]
    private ?string $role = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['staffs:read', 'staffs:write'])]
    #[ApiProperty(
        description: 'Numéro de téléphone du membre du staff',
        example: '+33612345678',
        required: false
    )]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['staffs:read', 'staffs:write'])]
    #[ApiProperty(
        description: 'Adresse email du membre du staff',
        example: 'john.doe@example.com',
        required: false
    )]
    private ?string $mail = null;

    #[ORM\OneToOne(inversedBy: 'staffs', cascade: ['persist', 'remove'])]
    #[Groups(['staffs:read', 'staffs:write'])]
    #[ApiProperty(
        description: 'Photo du membre du staff',
        example: ['data:image/jpeg;base64,...']
    )]
    private ?Images $image = null;

    /**
     * @var Collection<int, Teams>
     */
    #[ORM\ManyToMany(targetEntity: Teams::class, inversedBy: 'staffs')]
    #[Groups(['staffs:read', 'staffs:write'])]
    #[ApiProperty(
        description: 'Équipes associées au membre du staff',
        example: [1, 2]
    )]
    private Collection $team;

    public function __construct()
    {
        $this->team = new ArrayCollection();
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

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(?string $mail): static
    {
        $this->mail = $mail;

        return $this;
    }

    public function getImage(): ?Images
    {
        return $this->image;
    }

    public function setImage(?Images $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Teams>
     */
    public function getTeam(): Collection
    {
        return $this->team;
    }

    public function addTeam(Teams $team): static
    {
        if (!$this->team->contains($team)) {
            $this->team->add($team);
        }

        return $this;
    }

    public function removeTeam(Teams $team): static
    {
        $this->team->removeElement($team);

        return $this;
    }
}
