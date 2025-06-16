<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\IntroductionController;
use App\Repository\IntroductionRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: IntroductionRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/introduction/{id}',
            controller: IntroductionController::class . '::getIntroduction',
            normalizationContext: ['groups' => ['introduction:read']]
        ),
        new GetCollection(
            uriTemplate: '/introduction',
            controller: IntroductionController::class . '::getAllIntroductions',
            normalizationContext: ['groups' => ['introduction:read']]
        ),
        new Post(
            uriTemplate: '/introduction',
            controller: IntroductionController::class . '::createIntroduction',
            deserialize: false,
            denormalizationContext: ['groups' => ['introduction:write']]
        ),
        new Post(
            uriTemplate: '/introduction/{id}',
            controller: IntroductionController::class . '::updateIntroduction',
            deserialize: false,
            denormalizationContext: ['groups' => ['introduction:write']]
        ),
        new Delete(
            uriTemplate: '/introduction/{id}',
            controller: IntroductionController::class . '::deleteIntroduction'
        )
    ],
    formats: ['json', 'multipart' => ['multipart/form-data']]
)]
class Introduction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['introduction:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['introduction:read', 'introduction:write'])]
    private ?string $title = null;

    /**
     * @var Collection<int, Images>
     */
    #[ORM\OneToMany(targetEntity: Images::class, mappedBy: 'introduction')]
    #[Groups(['introduction:read'])]
    private Collection $image;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['introduction:read', 'introduction:write'])]
    private ?string $description = null;

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

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<int, Images>
     */
    public function getImage(): Collection
    {
        return $this->image;
    }

    public function addImage(Images $image): static
    {
        if (!$this->image->contains($image)) {
            $this->image->add($image);
            $image->setIntroduction($this);
        }

        return $this;
    }

    public function removeImage(Images $image): static
    {
        if ($this->image->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getIntroduction() === $this) {
                $image->setIntroduction(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
