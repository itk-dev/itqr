<?php

namespace App\Entity;

use App\Repository\UrlRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UrlRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Url
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $shortUri;

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

    #[ORM\PrePersist]
    public function setShortUri(): void
    {
        $this->shortUri = Uuid::v7()->hash();
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getQr(): ?Qr
    {
        return $this->qr;
    }

    public function setQr(?Qr $qr): self
    {
        $this->qr = $qr;

        return $this;
    }
}
