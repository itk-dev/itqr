<?php

namespace App\Entity\Tenant;

use App\Repository\UrlRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UrlRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Url extends AbstractTenantScopedEntity
{
    #[Assert\NotBlank(message: new TranslatableMessage('The URL field cannot be empty.'))]
    #[Assert\Url(message: new TranslatableMessage('The value "{{ value }}" is not a valid URL.'))]
    #[ORM\Column(type: 'text', length: 65535)]
    private string $url = '';

    #[ORM\ManyToOne(targetEntity: Qr::class, inversedBy: 'urls')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Qr $qr = null;

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

    public function __toString(): string
    {
        return $this->url;
    }
}
