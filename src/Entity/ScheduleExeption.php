<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ScheduleExeptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScheduleExeptionRepository::class)]
#[ApiResource]
class ScheduleExeption
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'scheduleExeptions')]
    private ?recurringSchedule $id_reccuring_schedule = null;

    #[ORM\Column]
    private ?\DateTime $exeption_date = null;

    #[ORM\Column]
    private ?\DateTime $start_time = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $end_time = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $location = null;

    #[ORM\Column(nullable: true)]
    private ?bool $is_cancelled = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $reason = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdReccuringSchedule(): ?recurringSchedule
    {
        return $this->id_reccuring_schedule;
    }

    public function setIdReccuringSchedule(?recurringSchedule $id_reccuring_schedule): static
    {
        $this->id_reccuring_schedule = $id_reccuring_schedule;

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
