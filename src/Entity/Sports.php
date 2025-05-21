<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\SportsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SportsRepository::class)]
#[ApiResource]
class Sports
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $contact = null;

    /**
     * @var Collection<int, Teams>
     */
    #[ORM\OneToMany(targetEntity: Teams::class, mappedBy: 'id_sport')]
    private Collection $teams;

    /**
     * @var Collection<int, Events>
     */
    #[ORM\OneToMany(targetEntity: Events::class, mappedBy: 'id_sport')]
    private Collection $events;

    /**
     * @var Collection<int, RecurringSchedule>
     */
    #[ORM\OneToMany(targetEntity: RecurringSchedule::class, mappedBy: 'id_sport')]
    private Collection $recurringSchedules;

    /**
     * @var Collection<int, images>
     */
    #[ORM\OneToMany(targetEntity: images::class, mappedBy: 'sports')]
    private Collection $image;

    public function __construct()
    {
        $this->teams = new ArrayCollection();
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
            $team->setIdSport($this);
        }

        return $this;
    }

    public function removeTeam(Teams $team): static
    {
        if ($this->teams->removeElement($team)) {
            // set the owning side to null (unless already changed)
            if ($team->getIdSport() === $this) {
                $team->setIdSport(null);
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
            $event->setIdSport($this);
        }

        return $this;
    }

    public function removeEvent(Events $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getIdSport() === $this) {
                $event->setIdSport(null);
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
            $recurringSchedule->setIdSport($this);
        }

        return $this;
    }

    public function removeRecurringSchedule(RecurringSchedule $recurringSchedule): static
    {
        if ($this->recurringSchedules->removeElement($recurringSchedule)) {
            // set the owning side to null (unless already changed)
            if ($recurringSchedule->getIdSport() === $this) {
                $recurringSchedule->setIdSport(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, images>
     */
    public function getImage(): Collection
    {
        return $this->image;
    }

    public function addImage(images $image): static
    {
        if (!$this->image->contains($image)) {
            $this->image->add($image);
            $image->setSports($this);
        }

        return $this;
    }

    public function removeImage(images $image): static
    {
        if ($this->image->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getSports() === $this) {
                $image->setSports(null);
            }
        }

        return $this;
    }
}
