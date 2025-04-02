<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TenantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TenantRepository::class)]
class Tenant extends AbstractBaseEntity implements \JsonSerializable
{
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 255, nullable: false, options: ['default' => ''])]
    private string $title = '';

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 255, nullable: false, options: ['default' => ''])]
    private string $description = '';

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 25, unique: true)]
    private string $tenantKey = '';

    /**
     * @var Collection<int, UserRoleTenant>
     */
    #[ORM\OneToMany(targetEntity: UserRoleTenant::class, mappedBy: 'tenant', orphanRemoval: true)]
    private Collection $userRoleTenants;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, nullable: true)]
    private ?string $fallbackImageUrl = null;

    public function __construct()
    {
        parent::__construct();

        $this->userRoleTenants = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getUserRoleTenants(): Collection
    {
        return $this->userRoleTenants;
    }

    public function addUserRoleTenant(UserRoleTenant $userRoleTenant): self
    {
        if (!$this->userRoleTenants->contains($userRoleTenant)) {
            $this->userRoleTenants[] = $userRoleTenant;
            $userRoleTenant->setTenant($this);
        }

        return $this;
    }

    public function removeUserRoleTenant(UserRoleTenant $userRoleTenant): self
    {
        if ($this->userRoleTenants->removeElement($userRoleTenant)) {
            // set the owning side to null (unless already changed)
            if ($userRoleTenant->getTenant() === $this) {
                $userRoleTenant->setTenant(null);
            }
        }

        return $this;
    }

    public function getTenantKey(): string
    {
        return $this->tenantKey;
    }

    public function setTenantKey(string $tenantKey): self
    {
        $this->tenantKey = $tenantKey;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'tenantKey' => $this->getTenantKey(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
        ];
    }
}
