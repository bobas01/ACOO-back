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
    private ?bool $isCancelled = null;

    #[ORM\Column]
    private ?\DateTimeInterface $startDatetime = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeInterface $endDatetime = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    private ?Sports $sport = null;

    #[ORM\ManyToMany(targetEntity: Teams::class, inversedBy: 'events')]
    private Collection $teams;

    #[ORM\OneToMany(targetEntity: News::class, mappedBy: 'event')]
    private Collection $news;

    #[ORM\OneToMany(targetEntity: Images::class, mappedBy: 'event')]
    private Collection $images;

    public function __construct()
    {
        $this->teams = new ArrayCollection();
        $this->news = new ArrayCollection();
        $this->images = new ArrayCollection();
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
        return $this->isCancelled;
    }

    public function setIsCancelled(bool $isCancelled): static
    {
        $this->isCancelled = $isCancelled;
        return $this;
    }

    public function getStartDatetime(): ?\DateTimeInterface
    {
        return $this->startDatetime;
    }

    public function setStartDatetime(\DateTimeInterface $startDatetime): static
    {
        $this->startDatetime = $startDatetime;
        return $this;
    }

    public function getEndDatetime(): ?\DateTimeInterface
    {
        return $this->endDatetime;
    }

    public function setEndDatetime(?\DateTimeInterface $endDatetime): static
    {
        $this->endDatetime = $endDatetime;
        return $this;
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
        }
        return $this;
    }

    public function removeTeam(Teams $team): static
    {
        $this->teams->removeElement($team);
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
            $news->setEvent($this);
        }
        return $this;
    }

    public function removeNews(News $news): static
    {
        if ($this->news->removeElement($news)) {
            if ($news->getEvent() === $this) {
                $news->setEvent(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Images>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Images $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setEvents($this);
        }
        return $this;
    }

    public function removeImage(Images $image): static
    {
        if ($this->images->removeElement($image)) {
            if ($image->getEvents() === $this) {
                $image->setEvents(null);
            }
        }
        return $this;
    }
}