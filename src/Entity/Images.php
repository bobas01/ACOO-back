<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ImagesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ImagesRepository::class)]
#[ApiResource]
class Images
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['social_media:read', 'social_media:write'])]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    #[Groups(['social_media:read', 'social_media:write'])]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'image')]
    private ?Introduction $introduction = null;

    #[ORM\ManyToOne(inversedBy: 'image')]
    private ?SocialMedias $socialMedias = null;

    #[ORM\ManyToOne(inversedBy: 'image')]
    private ?PrizeList $prizeList = null;

    #[ORM\ManyToOne(inversedBy: 'image')]
    private ?Sports $sports = null;

    #[ORM\ManyToOne(inversedBy: 'image')]
    private ?Events $events = null;

    #[ORM\ManyToOne(inversedBy: 'image')]
    private ?Pictures $pictures = null;

    #[ORM\ManyToOne(inversedBy: 'image')]
    private ?News $news = null;

    #[ORM\ManyToOne(inversedBy: 'image')]
    private ?Partners $partners = null;

    #[ORM\ManyToOne(inversedBy: 'image')]
    private ?Teams $teams = null;

    #[ORM\OneToOne(mappedBy: 'image', cascade: ['persist', 'remove'])]
    private ?Staffs $staffs = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function getTeams(): ?Teams
    {
        return $this->teams;
    }

    public function setTeams(?Teams $teams): static
    {
        $this->teams = $teams;
        return $this;
    }

    public function getIntroduction(): ?Introduction
    {
        return $this->introduction;
    }

    public function setIntroduction(?Introduction $introduction): static
    {
        $this->introduction = $introduction;

        return $this;
    }

    public function getSocialMedias(): ?SocialMedias
    {
        return $this->socialMedias;
    }

    public function setSocialMedias(?SocialMedias $socialMedias): static
    {
        $this->socialMedias = $socialMedias;

        return $this;
    }

    public function getPrizeList(): ?PrizeList
    {
        return $this->prizeList;
    }

    public function setPrizeList(?PrizeList $prizeList): static
    {
        $this->prizeList = $prizeList;

        return $this;
    }

    public function getSports(): ?Sports
    {
        return $this->sports;
    }

    public function setSports(?Sports $sports): static
    {
        $this->sports = $sports;

        return $this;
    }

    public function getEvents(): ?Events
    {
        return $this->events;
    }

    public function setEvents(?Events $events): static
    {
        $this->events = $events;

        return $this;
    }

    public function getPictures(): ?Pictures
    {
        return $this->pictures;
    }

    public function setPictures(?Pictures $pictures): static
    {
        $this->pictures = $pictures;

        return $this;
    }

    public function getNews(): ?News
    {
        return $this->news;
    }

    public function setNews(?News $news): static
    {
        $this->news = $news;

        return $this;
    }

    public function getPartners(): ?Partners
    {
        return $this->partners;
    }

    public function setPartners(?Partners $partners): static
    {
        $this->partners = $partners;

        return $this;
    }

    public function getStaffs(): ?Staffs
    {
        return $this->staffs;
    }

    public function setStaffs(?Staffs $staffs): static
    {
        // unset the owning side of the relation if necessary
        if ($staffs === null && $this->staffs !== null) {
            $this->staffs->setImage(null);
        }

        // set the owning side of the relation if necessary
        if ($staffs !== null && $staffs->getImage() !== $this) {
            $staffs->setImage($this);
        }

        $this->staffs = $staffs;

        return $this;
    }
}
