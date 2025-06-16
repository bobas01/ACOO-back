<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use App\Controller\ScheduleExeptionController;
use App\Repository\ScheduleExeptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ScheduleExeptionRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/schedule-exeptions/{id}',
            controller: ScheduleExeptionController::class . '::show',
            normalizationContext: ['groups' => ['schedule_exeption:read']]
        ),
        new GetCollection(
            uriTemplate: '/schedule-exeptions',
            controller: ScheduleExeptionController::class . '::index',
            normalizationContext: ['groups' => ['schedule_exeption:read']]
        ),
        new Post(
            uriTemplate: '/schedule-exeptions',
            controller: ScheduleExeptionController::class . '::create',
            deserialize: false,
            denormalizationContext: ['groups' => ['schedule_exeption:write']]
        ),
        new Post(
            uriTemplate: '/schedule-exeptions/{id}',
            controller: ScheduleExeptionController::class . '::update',
            deserialize: false,
            denormalizationContext: ['groups' => ['schedule_exeption:write']]
        ),
        new Delete(
            uriTemplate: '/schedule-exeptions/{id}',
            controller: ScheduleExeptionController::class . '::delete'
        )
    ],
    formats: ['json', 'multipart' => ['multipart/form-data']]
)]
class ScheduleExeption
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['schedule_exeption:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'scheduleExeptions')]
    #[Groups(['schedule_exeption:read', 'schedule_exeption:write'])]
    private ?RecurringSchedule $recurring_schedule = null;

    #[ORM\Column]
    #[Groups(['schedule_exeption:read', 'schedule_exeption:write'])]
    private ?\DateTime $exeption_date = null;

    #[ORM\Column]
    #[Groups(['schedule_exeption:read', 'schedule_exeption:write'])]
    private ?\DateTime $start_time = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['schedule_exeption:read', 'schedule_exeption:write'])]
    private ?\DateTime $end_time = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['schedule_exeption:read', 'schedule_exeption:write'])]
    private ?string $location = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['schedule_exeption:read', 'schedule_exeption:write'])]
    private ?bool $is_cancelled = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['schedule_exeption:read', 'schedule_exeption:write'])]
    private ?string $reason = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRecurringSchedule(): ?RecurringSchedule
    {
        return $this->recurring_schedule;
    }

    public function setRecurringSchedule(?RecurringSchedule $recurring_schedule): static
    {
        $this->recurring_schedule = $recurring_schedule;
        return $this;
    }

    public function getExeptionDate(): ?\DateTime
    {
        return $this->exeption_date;
    }

    public function setExeptionDate(\DateTime $exeption_date): static
    {
        $this->exeption_date = $exeption_date;
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

    public function getEndTime(): ?\DateTime
    {
        return $this->end_time;
    }

    public function setEndTime(?\DateTime $end_time): static
    {
        $this->end_time = $end_time;
        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): static
    {
        $this->location = $location;
        return $this;
    }

    public function isCancelled(): ?bool
    {
        return $this->is_cancelled;
    }

    public function setIsCancelled(?bool $is_cancelled): static
    {
        $this->is_cancelled = $is_cancelled;
        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): static
    {
        $this->reason = $reason;
        return $this;
    }
}
