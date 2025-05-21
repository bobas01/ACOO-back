<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PicturesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PicturesRepository::class)]
#[ApiResource]
class Pictures
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

   

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'pictures')]
    private ?gallery $id_gallery = null;

    /**
     * @var Collection<int, images>
     */
    #[ORM\OneToMany(targetEntity: images::class, mappedBy: 'pictures')]
    private Collection $image;

    public function __construct()
    {
        $this->image = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIdGallery(): ?gallery
    {
        return $this->id_gallery;
    }

    public function setIdGallery(?gallery $id_gallery): static
    {
        $this->id_gallery = $id_gallery;

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
            $image->setPictures($this);
        }

        return $this;
    }

    public function removeImage(images $image): static
    {
        if ($this->image->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getPictures() === $this) {
                $image->setPictures(null);
            }
        }

        return $this;
    }
}
