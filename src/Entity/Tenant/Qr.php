<?php

namespace App\Entity\Tenant;

use ApiPlatform\Doctrine\Orm\Filter\BackedEnumFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\QrHitTracker;
use App\Enum\QrModeEnum;
use App\Enum\QrStatusEnum;
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

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $alternativeUrl = null;

    #[ORM\Column(type: 'string', enumType: QrModeEnum::class)]
    #[ApiFilter(BackedEnumFilter::class)]
    private QrModeEnum $mode;

    #[ORM\Column(type: 'string', enumType: QrStatusEnum::class)]
    #[ApiFilter(BackedEnumFilter::class)]
    private QrStatusEnum $status;

    /**
     * @var Collection<int, Url>
     */
    #[ORM\OneToMany(targetEntity: Url::class, mappedBy: 'qr', cascade: ['persist', 'remove'], fetch: 'EAGER', orphanRemoval: true)]
    #[Assert\Valid]
    private Collection $urls;

    /**
     * @var Collection<int, QrHitTracker>
     */
    #[ORM\OneToMany(targetEntity: QrHitTracker::class, mappedBy: 'qr')]
    private Collection $hitTrackers;

    public function __construct()
    {
        parent::__construct();

        $this->urls = new ArrayCollection();
        $this->hitTrackers = new ArrayCollection();
        $this->uuid = Uuid::v7();
        $this->status = QrStatusEnum::ACTIVE;
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

    public function getAlternativeUrl(): ?string
    {
        return $this->alternativeUrl;
    }

    public function setAlternativeUrl(?string $alternativeUrl): self
    {
        $this->alternativeUrl = $alternativeUrl;

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

            /*
             * We have to set the tenant here because there is no central place to hook into and do it for embedded
             * entities. The "createEntity() function is not called when the EasyAdmin crud controller is used as an
             * embedded controller. EasyAdmin and doctrines events are also useless because validation is run before
             * them, and if validation fails, an error is shown to the user.
             */
            $url->setTenant($this->getTenant());
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

    public function getHitTrackers(): Collection
    {
        return $this->hitTrackers;
    }

    public function getStatus(): QrStatusEnum
    {
        return $this->status;
    }

    public function setStatus(QrStatusEnum $status): self
    {
        $this->status = $status;

        return $this;
    }

}
