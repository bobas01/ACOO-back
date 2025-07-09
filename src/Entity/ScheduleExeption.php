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
use ApiPlatform\Metadata\ApiProperty;

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
    #[ApiProperty(description: 'Identifiant unique de l\'exception')]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'scheduleExeptions', targetEntity: RecurringSchedule::class)]
    #[Groups(['schedule_exeption:read', 'schedule_exeption:write', 'recurring_schedule:read'])]
    #[ApiProperty(
        description: 'Planning récurrent concerné',
        example: 1,
        required: true
    )]
    private ?RecurringSchedule $recurring_schedule = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['schedule_exeption:read', 'schedule_exeption:write'])]
    #[ApiProperty(
        description: 'Date de l\'exception',
        example: '2024-03-25T14:00:00+00:00',
        required: true
    )]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Groups(['schedule_exeption:read', 'schedule_exeption:write'])]
    #[ApiProperty(
        description: 'Heure de début',
        example: '14:00:00',
        required: true
    )]
    private ?\DateTimeInterface $startTime = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Groups(['schedule_exeption:read', 'schedule_exeption:write'])]
    #[ApiProperty(
        description: 'Heure de fin',
        example: '16:00:00',
        required: true
    )]
    private ?\DateTimeInterface $endTime = null;

    #[ORM\Column(length: 255)]
    #[Groups(['schedule_exeption:read', 'schedule_exeption:write'])]
    #[ApiProperty(
        description: 'Lieu de l\'entraînement',
        example: 'Gymnase municipal',
        required: true
    )]
    private ?string $location = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['schedule_exeption:read', 'schedule_exeption:write'])]
    private ?bool $is_cancelled = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['schedule_exeption:read', 'schedule_exeption:write'])]
    private ?string $reason = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['schedule_exeption:read'])]
    #[ApiProperty(description: 'Date de création de l\'exception')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['schedule_exeption:read'])]
    #[ApiProperty(description: 'Date de dernière mise à jour de l\'exception')]
    private ?\DateTimeImmutable $updatedAt = null;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): static
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(?\DateTimeInterface $endTime): static
    {
        $this->endTime = $endTime;
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
