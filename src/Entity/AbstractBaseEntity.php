<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Interfaces\BlameableInterface;
use App\Entity\Interfaces\TimestampableInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
abstract class AbstractBaseEntity implements BlameableInterface, TimestampableInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::DATETIME_IMMUTABLE, nullable: false)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::DATETIME_IMMUTABLE, nullable: false)]
    private \DateTimeImmutable $modifiedAt;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, nullable: false, options: ['default' => ''])]
    private string $createdBy = '';

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, nullable: false, options: ['default' => ''])]
    private string $modifiedBy = '';

    public function __construct()
    {
        $this->modifiedAt = new \DateTimeImmutable();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt = null): self
    {
        $createdAt ??= new \DateTimeImmutable();

        $this->createdAt = \DateTimeImmutable::createFromInterface($createdAt);

        return $this;
    }

    public function getModifiedAt(): ?\DateTimeImmutable
    {
        return $this->modifiedAt ?? null;
    }

    public function setModifiedAt(?\DateTimeInterface $modifiedAt = null): self
    {
        $this->modifiedAt = $modifiedAt ? \DateTimeImmutable::createFromInterface($modifiedAt) : new \DateTimeImmutable();

        return $this;
    }

    public function getModifiedBy(): string
    {
        return $this->modifiedBy;
    }

    public function setModifiedBy(string $modifiedBy): self
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    public function getCreatedBy(): string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}
