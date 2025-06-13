<?php

namespace App\Entity;

use App\Entity\Tenant\Qr;
use App\Repository\QrHitTrackerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QrHitTrackerRepository::class)]
class QrHitTracker
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Qr::class, inversedBy: 'urls')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Qr $qr = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $timestamp = null;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getQr(): Qr
    {
        return $this->qr;
    }

    public function setQr(Qr $qr): static
    {
        $this->qr = $qr;

        return $this;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeInterface $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }
}
