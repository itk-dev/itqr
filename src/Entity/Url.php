<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UrlRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UrlRepository::class)]
class Url
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $shortUri = '';

    #[ORM\Column(length: 255)]
    private string $url = '';

    #[ORM\ManyToOne(inversedBy: 'urls')]
    private ?Qr $qr = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShortUri(): ?string
    {
        return $this->shortUri;
    }

    public function setShortUri(string $shortUri): static
    {
        $this->shortUri = $shortUri;

        return $this;
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

    public function getQr(): ?Qr
    {
        return $this->qr;
    }

    public function setQr(?Qr $qr): static
    {
        $this->qr = $qr;

        return $this;
    }
}
