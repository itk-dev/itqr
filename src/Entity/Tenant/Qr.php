<?php

namespace App\Entity\Tenant;

use ApiPlatform\Doctrine\Orm\Filter\BackedEnumFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Enum\QrModeEnum;
use App\Repository\QrRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource]
#[ORM\Entity(repositoryClass: QrRepository::class)]
class Qr extends AbstractTenantScopedEntity
{
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Assert\NotNull(message: 'The UUID field cannot be empty.')]
    private ?UuidV7 $uuid;

    #[ORM\Column(length: 255)]
    #[ApiFilter(SearchFilter::class, strategy: 'partial')]
    private string $title = '';

    #[ORM\Column(length: 255)]
    #[ApiFilter(SearchFilter::class, strategy: 'partial')]
    private string $department = '';

    #[ORM\Column(length: 2500, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', enumType: QrModeEnum::class)]
    #[ApiFilter(BackedEnumFilter::class)]
    private QrModeEnum $mode;

    /**
     * @var Collection<int, Url>
     */
    #[ORM\OneToMany(targetEntity: Url::class, mappedBy: 'qr', cascade: ['persist', 'remove'], fetch: 'EAGER', orphanRemoval: true)]
    #[Assert\Valid]
    private Collection $urls;

    public function __construct()
    {
        parent::__construct();

        $this->urls = new ArrayCollection();
        $this->uuid = Uuid::v7();
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function setDepartment(string $department): self
    {
        $this->department = $department;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getMode(): ?QrModeEnum
    {
        return $this->mode;
    }

    public function setMode(?QrModeEnum $mode): self
    {
        $this->mode = $mode;

        return $this;
    }

    public function getUuid(): ?UuidV7
    {
        return $this->uuid;
    }

    /**
     * @return Collection<int, Url>
     */
    public function getUrls(): Collection
    {
        return $this->urls;
    }

    public function addUrl(Url $url): self
    {
        if (!$this->urls->contains($url)) {
            $this->urls[] = $url;
            $url->setQr($this);
        }

        return $this;
    }

    public function removeUrl(Url $url): self
    {
        if ($this->urls->contains($url)) {
            $this->urls->removeElement($url);

            if ($url->getQr() === $this) {
                $url->setQr(null);
            }
        }

        return $this;
    }

    public function removeAllUrls(): void
    {
        foreach ($this->urls as $url) {
            $this->removeUrl($url);
        }
    }
}
