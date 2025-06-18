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
use ApiPlatform\Metadata\ApiProperty;

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
    #[Groups(['news:read'])]
    #[ApiProperty(description: 'Identifiant unique de l\'actualité')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['news:read', 'news:write'])]
    #[ApiProperty(
        description: 'Titre de l\'actualité',
        example: 'Nouveau record battu',
        required: true
    )]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['news:read', 'news:write'])]
    #[ApiProperty(
        description: 'Contenu de l\'actualité',
        example: 'Un nouveau record a été battu lors du championnat...',
        required: true
    )]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['news:read'])]
    #[ApiProperty(
        description: 'Date de publication de l\'actualité',
        example: '2024-03-20T10:00:00+00:00'
    )]
    private ?\DateTimeInterface $publishedAt = null;

    #[ORM\ManyToOne(inversedBy: 'news')]
    #[Groups(['news:read', 'news:write'])]
    #[ApiProperty(
        description: 'Événement associé à l\'actualité',
        example: 1
    )]
    private ?Events $event = null;

    #[ORM\OneToMany(targetEntity: Images::class, mappedBy: 'news')]
    #[Groups(['news:read', 'news:write'])]
    #[ApiProperty(
        description: 'Images associées à l\'actualité (tableau base64 pour upload)',
        example: ['data:image/jpeg;base64,...'],
        required: false
    )]
    private Collection $images;

    #[ORM\Column(length: 255, nullable: true)]
    #[ApiProperty(readable: false, writable: false)]
    private ?string $imgUrl = null;

    #[ORM\Column]
    #[ApiProperty(readable: false, writable: false)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    #[ApiProperty(readable: false, writable: false)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne(inversedBy: 'news')]
    #[ApiProperty(readable: false, writable: false)]
    private ?admin $id_admin = null;

    public function __construct()
    {
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

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeInterface $publishedAt): static
    {
        $this->publishedAt = $publishedAt;

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

    public function getImgUrl(): ?string
    {
        return $this->imgUrl;
    }

    public function setImgUrl(?string $imgUrl): static
    {
        $this->imgUrl = $imgUrl;

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
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(images $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setNews($this);
        }

        return $this;
    }

    public function removeImage(images $image): static
    {
        if ($this->images->removeElement($image)) {
            
            if ($image->getNews() === $this) {
                $image->setNews(null);
            }
        }

        return $this;
    }
}
