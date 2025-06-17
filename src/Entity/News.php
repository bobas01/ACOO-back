<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\NewsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Admin;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use App\Controller\NewsController;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: NewsRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/news/{id}',
            controller: NewsController::class . '::getNews',
            normalizationContext: ['groups' => ['news:read']]
        ),
        new GetCollection(
            uriTemplate: '/news',
            controller: NewsController::class . '::getAllNews',
            normalizationContext: ['groups' => ['news:read']]
        ),
        new Post(
            uriTemplate: '/news',
            controller: NewsController::class . '::createNews',
            deserialize: false,
            denormalizationContext: ['groups' => ['news:write']]
        ),
        new Post(
            uriTemplate: '/news/{id}',
            controller: NewsController::class . '::updateNews',
            deserialize: false,
            denormalizationContext: ['groups' => ['news:write']]
        ),
        new Delete(
            uriTemplate: '/news/{id}',
            controller: NewsController::class . '::deleteNews'
        )
    ],
    formats: ['json', 'multipart' => ['multipart/form-data']]
)]
class News
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['news:read', 'news:write'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['news:read', 'news:write'])]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['news:read', 'news:write'])]
    private ?string $imgUrl = null;

    #[ORM\ManyToOne(targetEntity: Events::class, inversedBy: 'news')]
    #[Groups(['news:read', 'news:write'])]
    private ?Events $event = null;

    #[ORM\Column]
    #[Groups(['news:read', 'news:write'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['news:read', 'news:write'])]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne(inversedBy: 'news')]
    private ?admin $id_admin = null;

    /**
     * @var Collection<int, images>
     */
    #[ORM\OneToMany(targetEntity: Images::class, mappedBy: 'news')]
    private Collection $image;

    public function __construct()
    {
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImgUrl(): ?string
    {
        return $this->imgUrl;
    }

    public function setImgUrl(?string $imgUrl): static
    {
        $this->imgUrl = $imgUrl;

        return $this;
    }

    public function getEvent(): ?Events
    {
        return $this->event;
    }

    public function setEvent(?Events $event): static
    {
        $this->event = $event;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getIdAdmin(): ?admin
    {
        return $this->id_admin;
    }

    public function setIdAdmin(?admin $id_admin): static
    {
        $this->id_admin = $id_admin;

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
            $image->setNews($this);
        }

        return $this;
    }

    public function removeImage(images $image): static
    {
        if ($this->image->removeElement($image)) {
            
            if ($image->getNews() === $this) {
                $image->setNews(null);
            }
        }

        return $this;
    }
}
