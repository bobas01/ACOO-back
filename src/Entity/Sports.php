<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\SportsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Controller\SportsController;
use ApiPlatform\Metadata\ApiProperty;

#[ORM\Entity(repositoryClass: SportsRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/sports/{id}',
            controller: SportsController::class . '::getSport',
            normalizationContext: ['groups' => ['sports:read']]
        ),
        new GetCollection(
            uriTemplate: '/sports',
            controller: SportsController::class . '::getAllSports',
            normalizationContext: ['groups' => ['sports:read']]
        ),
        new Post(
            uriTemplate: '/sports',
            controller: SportsController::class . '::createSport',
            deserialize: false,
            denormalizationContext: ['groups' => ['sports:write']]
        ),
        new Post(
            uriTemplate: '/sports/{id}',
            controller: SportsController::class . '::updateSport',
            deserialize: false,
            denormalizationContext: ['groups' => ['sports:write']]
        ),
        new Delete(
            uriTemplate: '/sports/{id}',
            controller: SportsController::class . '::deleteSport'
        )
    ],
    formats: ['json', 'multipart' => ['multipart/form-data']]
)]
class Sports
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['sports:read'])]
    #[ApiProperty(description: 'Identifiant unique du sport')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['sports:read', 'sports:write'])]
    #[ApiProperty(
        description: 'Nom du sport',
        example: 'Athlétisme',
        required: true
    )]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['sports:read', 'sports:write'])]
    #[ApiProperty(
        description: 'Description du sport',
        example: 'L\'athlétisme est un sport qui regroupe plusieurs disciplines...',
        required: true
    )]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['sports:read', 'sports:write'])]
    private ?string $contact = null;

    /**
     * @var Collection<int, Teams>
     */
    #[ORM\OneToMany(targetEntity: Teams::class, mappedBy: 'id_sport')]
    #[Groups(['sports:read'])]
    private Collection $teams;

    /**
     * @var Collection<int, Events>
     */
    #[ORM\OneToMany(targetEntity: Events::class, mappedBy: 'id_sport')]
    #[Groups(['sports:read'])]
    #[ApiProperty(description: 'Événements associés au sport')]
    private Collection $events;

    /**
     * @var Collection<int, RecurringSchedule>
     */
    #[ORM\OneToMany(targetEntity: RecurringSchedule::class, mappedBy: 'id_sport')]
    #[Groups(['sports:read'])]
    private Collection $recurringSchedules;

    /**
     * @var Collection<int, images>
     */
    #[ORM\OneToMany(targetEntity: Images::class, mappedBy: 'sports')]
    #[Groups(['sports:read', 'sports:write'])]
    #[ApiProperty(
        description: 'Images associées au sport',
        example: ['data:image/jpeg;base64,...']
    )]
    private Collection $images;

    public function __construct()
    {
        $this->teams = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->recurringSchedules = new ArrayCollection();
        $this->images = new ArrayCollection();
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

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(?string $contact): static
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * @return Collection<int, Teams>
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Teams $team): static
    {
        if (!$this->teams->contains($team)) {
            $this->teams->add($team);
            $team->setSport($this);
        }

        return $this;
    }

    public function removeTeam(Teams $team): static
    {
        if ($this->teams->removeElement($team)) {
            
            if ($team->getSport() === $this) {
                $team->setSport(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Events>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Events $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setSport($this);
        }

        return $this;
    }

    public function removeEvent(Events $event): static
    {
        if ($this->events->removeElement($event)) {
            if ($event->getSport() === $this) {
                $event->setSport(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RecurringSchedule>
     */
    public function getRecurringSchedules(): Collection
    {
        return $this->recurringSchedules;
    }

    public function addRecurringSchedule(RecurringSchedule $recurringSchedule): static
    {
        if (!$this->recurringSchedules->contains($recurringSchedule)) {
            $this->recurringSchedules->add($recurringSchedule);
            $recurringSchedule->setSport($this);
        }

        return $this;
    }

    public function removeRecurringSchedule(RecurringSchedule $recurringSchedule): static
    {
        if ($this->recurringSchedules->removeElement($recurringSchedule)) {
            if ($recurringSchedule->getSport() === $this) {
                $recurringSchedule->setSport(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, images>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(images $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setSports($this);
        }

        return $this;
    }

    public function removeImage(images $image): static
    {
        if ($this->images->removeElement($image)) {
            if ($image->getSports() === $this) {
                $image->setSports(null);
            }
        }

        return $this;
    }
}
