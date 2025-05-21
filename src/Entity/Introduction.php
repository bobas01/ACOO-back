<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\IntroductionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IntroductionRepository::class)]
#[ApiResource]
class Introduction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

  

    /**
     * @var Collection<int, images>
     */
    #[ORM\OneToMany(targetEntity: images::class, mappedBy: 'introduction')]
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

    public function setTitle(?string $title): static
    {
        $this->title = $title;

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
            $image->setIntroduction($this);
        }

        return $this;
    }

    public function removeImage(images $image): static
    {
        if ($this->image->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getIntroduction() === $this) {
                $image->setIntroduction(null);
            }
        }

        return $this;
    }
}
