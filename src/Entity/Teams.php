<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use App\Controller\TeamsController;
use App\Repository\TeamsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiProperty;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: TeamsRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/teams/{id}',
            controller: TeamsController::class . '::show',
            normalizationContext: ['groups' => ['teams:read']]
        ),
        new GetCollection(
            uriTemplate: '/teams',
            controller: TeamsController::class . '::index',
            normalizationContext: ['groups' => ['teams:read']]
        ),
        new Post(
            uriTemplate: '/teams',
            controller: TeamsController::class . '::create',
            deserialize: false,
            denormalizationContext: ['groups' => ['teams:write']]
        ),
        new Post(
            uriTemplate: '/teams/{id}',
            controller: TeamsController::class . '::update',
            deserialize: false,
            denormalizationContext: ['groups' => ['teams:write']]
        ),
        new Delete(
            uriTemplate: '/teams/{id}',
            controller: TeamsController::class . '::delete'
        )
    ],
    formats: ['json', 'multipart' => ['multipart/form-data']]
)]
class Teams
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['teams:read'])]
    #[ApiProperty(description: 'Identifiant unique de l\'équipe')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['teams:read', 'teams:write'])]
    #[ApiProperty(
        description: 'Nom de l\'équipe',
        example: 'Équipe A',
        required: true
    )]
    private ?string $name = null;


    #[ORM\ManyToOne(inversedBy: 'teams')]
    #[Groups(['teams:read', 'teams:write'])]
    #[ApiProperty(
        description: 'Sport pratiqué par l\'équipe',
        example: 1,
        required: true
    )]
    private ?Sports $id_sport = null;

    /**
     * @var Collection<int, Events>
     */
    #[ORM\ManyToMany(targetEntity: Events::class, mappedBy: 'teams')]
    #[Groups(['teams:read'])]
    #[ApiProperty(description: 'Événements auxquels l\'équipe participe')]
    private Collection $events;

    /**
     * @var Collection<int, RecurringSchedule>
     */
    #[ORM\OneToMany(targetEntity: RecurringSchedule::class, mappedBy: 'team')]
    #[Groups(['teams:read'])]
    private Collection $recurringSchedules;

    #[ORM\Column(length: 255)]
    #[Groups(['teams:read', 'teams:write'])]
    private ?string $role = null;

    #[ORM\OneToMany(targetEntity: Images::class, mappedBy: 'teams')]
    #[Groups(['teams:read', 'teams:write'])]
    #[ApiProperty(
        description: 'Images associées à l\'équipe',
        example: ['data:image/jpeg;base64,...']
    )]
    private Collection $image;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->recurringSchedules = new ArrayCollection();
        $this->image = new ArrayCollection();
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



    public function getSport(): ?Sports
    {
        return $this->id_sport;
    }

    public function setSport(?Sports $sport): static
    {
        $this->id_sport = $sport;
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
            $event->addTeam($this);
        }
        return $this;
    }

    public function removeEvent(Events $event): static
    {
        if ($this->events->removeElement($event)) {
            $event->removeTeam($this);
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
            $recurringSchedule->setTeam($this);
        }
        return $this;
    }

    public function removeRecurringSchedule(RecurringSchedule $recurringSchedule): static
    {
        if ($this->recurringSchedules->removeElement($recurringSchedule)) {
            if ($recurringSchedule->getTeam() === $this) {
                $recurringSchedule->setTeam(null);
            }
        }
        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @return Collection<int, Images>
     */
    public function getImages(): Collection
    {
        return $this->image;
    }

    public function addImage(Images $image): static
    {
        if (!$this->image->contains($image)) {
            $this->image->add($image);
            $image->setTeams($this);
        }
        return $this;
    }

    public function removeImage(Images $image): static
    {
        if ($this->image->removeElement($image)) {
            if ($image->getTeams() === $this) {
                $image->setTeams(null);
            }
        }
        return $this;
    }
}
