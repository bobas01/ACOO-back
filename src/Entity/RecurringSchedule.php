<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\RecurringScheduleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecurringScheduleRepository::class)]
#[ApiResource]
class RecurringSchedule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'recurringSchedules')]
    private ?sports $id_sport = null;

    #[ORM\ManyToOne(inversedBy: 'recurringSchedules')]
    private ?teams $id_team = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $location = null;

    #[ORM\Column]
    private ?\DateTime $start_time = null;

    #[ORM\Column(nullable: true)]
    private ?int $duration = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $frequency = null;

    #[ORM\Column]
    private ?\DateTime $end_date = null;

    #[ORM\Column(length: 255)]
    private ?string $day_of_week = null;

    /**
     * @var Collection<int, ScheduleExeption>
     */
    #[ORM\OneToMany(targetEntity: ScheduleExeption::class, mappedBy: 'id_reccuring_schedule')]
    private Collection $scheduleExeptions;

    public function __construct()
    {
        $this->scheduleExeptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdSport(): ?sports
    {
        return $this->id_sport;
    }

    public function setIdSport(?sports $id_sport): static
    {
        $this->id_sport = $id_sport;

        return $this;
    }

    public function getIdTeam(): ?teams
    {
        return $this->id_team;
    }

    public function setIdTeam(?teams $id_team): static
    {
        $this->id_team = $id_team;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getStartTime(): ?\DateTime
    {
        return $this->start_time;
    }

    public function setStartTime(\DateTime $start_time): static
    {
        $this->start_time = $start_time;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getFrequency(): ?string
    {
        return $this->frequency;
    }

    public function setFrequency(?string $frequency): static
    {
        $this->frequency = $frequency;

        return $this;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->end_date;
    }

    public function setEndDate(\DateTime $end_date): static
    {
        $this->end_date = $end_date;

        return $this;
    }

    public function getDayOfWeek(): ?string
    {
        return $this->day_of_week;
    }

    public function setDayOfWeek(string $day_of_week): static
    {
        $this->day_of_week = $day_of_week;

        return $this;
    }

    /**
     * @return Collection<int, ScheduleExeption>
     */
    public function getScheduleExeptions(): Collection
    {
        return $this->scheduleExeptions;
    }

    public function addScheduleExeption(ScheduleExeption $scheduleExeption): static
    {
        if (!$this->scheduleExeptions->contains($scheduleExeption)) {
            $this->scheduleExeptions->add($scheduleExeption);
            $scheduleExeption->setIdReccuringSchedule($this);
        }

        return $this;
    }

    public function removeScheduleExeption(ScheduleExeption $scheduleExeption): static
    {
        if ($this->scheduleExeptions->removeElement($scheduleExeption)) {
            // set the owning side to null (unless already changed)
            if ($scheduleExeption->getIdReccuringSchedule() === $this) {
                $scheduleExeption->setIdReccuringSchedule(null);
            }
        }

        return $this;
    }
}
