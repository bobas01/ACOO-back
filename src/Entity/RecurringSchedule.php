<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\RecurringScheduleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use App\Controller\RecurringScheduleController;
use Symfony\Component\Serializer\Annotation\Groups;



#[ORM\Entity(repositoryClass: RecurringScheduleRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/recurring-schedules/{id}',
            controller: RecurringScheduleController::class . '::getRecurringSchedule',
            normalizationContext: ['groups' => ['recurring_schedule:read']]
        ),
        new GetCollection(
            uriTemplate: '/recurring-schedules',
            controller: RecurringScheduleController::class . '::getAllRecurringSchedules',
            normalizationContext: ['groups' => ['recurring_schedule:read']]
        ),
        new Post(
            uriTemplate: '/recurring-schedules',
            controller: RecurringScheduleController::class . '::createRecurringSchedule',
            deserialize: false,
            denormalizationContext: ['groups' => ['recurring_schedule:write']]
        ),
        new Post(
            uriTemplate: '/recurring-schedules/{id}',
            controller: RecurringScheduleController::class . '::updateRecurringSchedule',
            deserialize: false,
            denormalizationContext: ['groups' => ['recurring_schedule:write']]
        ),
        new Delete(
            uriTemplate: '/recurring-schedules/{id}',
            controller: RecurringScheduleController::class . '::deleteRecurringSchedule'
        )
    ],
    formats: ['json', 'multipart' => ['multipart/form-data']]
)]
class RecurringSchedule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['recurring_schedule:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'recurringSchedules')]
    #[Groups(['recurring_schedule:read', 'recurring_schedule:write'])]
    private ?Sports $sport = null;

    #[ORM\ManyToOne(inversedBy: 'recurringSchedules')]
    #[Groups(['recurring_schedule:read', 'recurring_schedule:write'])]
    private ?Teams $team = null;

    #[ORM\Column(length: 255)]
    #[Groups(['recurring_schedule:read', 'recurring_schedule:write'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['recurring_schedule:read', 'recurring_schedule:write'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups(['recurring_schedule:read', 'recurring_schedule:write'])]
    private ?string $location = null;

    #[ORM\Column]
    #[Groups(['recurring_schedule:read', 'recurring_schedule:write'])]
    private ?\DateTime $start_time = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['recurring_schedule:read', 'recurring_schedule:write'])]
    private ?int $duration = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['recurring_schedule:read', 'recurring_schedule:write'])]
    private ?string $frequency = null;

    #[ORM\Column]
    #[Groups(['recurring_schedule:read', 'recurring_schedule:write'])]
    private ?\DateTime $end_date = null;

    #[ORM\Column(length: 255)]
    #[Groups(['recurring_schedule:read', 'recurring_schedule:write'])]
    private ?string $day_of_week = null;

    /**
     * @var Collection<int, ScheduleExeption>
     */
    #[ORM\OneToMany(targetEntity: ScheduleExeption::class, mappedBy: 'recurring_schedule')]
    private Collection $scheduleExeptions;

    public function __construct()
    {
        $this->scheduleExeptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSport(): ?Sports
    {
        return $this->sport;
    }

    public function setSport(?Sports $sport): static
    {
        $this->sport = $sport;
        return $this;
    }

    public function getTeam(): ?Teams
    {
        return $this->team;
    }

    public function setTeam(?Teams $team): static
    {
        $this->team = $team;
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
            $scheduleExeption->setRecurringSchedule($this);
        }
        return $this;
    }

    public function removeScheduleExeption(ScheduleExeption $scheduleExeption): static
    {
        if ($this->scheduleExeptions->removeElement($scheduleExeption)) {
            if ($scheduleExeption->getRecurringSchedule() === $this) {
                $scheduleExeption->setRecurringSchedule(null);
            }
        }
        return $this;
    }
}
