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

    #[ORM\Column(length: 255)]
    #[Groups(['social_media:read', 'social_media:write'])]
    private ?string $url = null;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

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
}
