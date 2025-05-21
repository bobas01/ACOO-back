<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\EventsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventsRepository::class)]
#[ApiResource]
class Events
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(length: 255)]
    private ?string $eventType = null;

    #[ORM\Column(length: 255)]
    private ?string $location = null;

    #[ORM\Column]
    private ?bool $is_cancelled = null;

    #[ORM\Column]
    private ?\DateTime $start_datetime = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $end_datetime = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    private ?sports $id_sport = null;

    /**
     * @var Collection<int, teams>
     */
    #[ORM\ManyToMany(targetEntity: teams::class, inversedBy: 'events')]
    private Collection $id_team;

    /**
     * @var Collection<int, News>
     */
    #[ORM\OneToMany(targetEntity: News::class, mappedBy: 'id_event')]
    private Collection $news;

    /**
     * @var Collection<int, images>
     */
    #[ORM\OneToMany(targetEntity: images::class, mappedBy: 'events')]
    private Collection $image;

    public function __construct()
    {
        $this->id_team = new ArrayCollection();
        $this->news = new ArrayCollection();
        $this->image = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getEventType(): ?string
    {
        return $this->eventType;
    }

    public function setEventType(string $eventType): static
    {
        $this->eventType = $eventType;

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

    public function isCancelled(): ?bool
    {
        return $this->is_cancelled;
    }

    public function setIsCancelled(bool $is_cancelled): static
    {
        $this->is_cancelled = $is_cancelled;

        return $this;
    }

    public function getStartDatetime(): ?\DateTime
    {
        return $this->start_datetime;
    }

    public function setStartDatetime(\DateTime $start_datetime): static
    {
        $this->start_datetime = $start_datetime;

        return $this;
    }

    public function getEndDatetime(): ?\DateTime
    {
        return $this->end_datetime;
    }

    public function setEndDatetime(?\DateTime $end_datetime): static
    {
        $this->end_datetime = $end_datetime;

        return $this;
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

    /**
     * @return Collection<int, teams>
     */
    public function getIdTeam(): Collection
    {
        return $this->id_team;
    }

    public function addIdTeam(teams $idTeam): static
    {
        if (!$this->id_team->contains($idTeam)) {
            $this->id_team->add($idTeam);
        }

        return $this;
    }

    public function removeIdTeam(teams $idTeam): static
    {
        $this->id_team->removeElement($idTeam);

        return $this;
    }

    /**
     * @return Collection<int, News>
     */
    public function getNews(): Collection
    {
        return $this->news;
    }

    public function addNews(News $news): static
    {
        if (!$this->news->contains($news)) {
            $this->news->add($news);
            $news->setIdEvent($this);
        }

        return $this;
    }

    public function removeNews(News $news): static
    {
        if ($this->news->removeElement($news)) {
            // set the owning side to null (unless already changed)
            if ($news->getIdEvent() === $this) {
                $news->setIdEvent(null);
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
            $image->setEvents($this);
        }

        return $this;
    }

    public function removeImage(images $image): static
    {
        if ($this->image->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getEvents() === $this) {
                $image->setEvents(null);
            }
        }

        return $this;
    }
}
