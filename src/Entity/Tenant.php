<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TenantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types as DoctrineTypes;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TenantRepository::class)]
#[ORM\UniqueConstraint(name: "name_unique", columns: ["name"])]
class Tenant extends AbstractBaseEntity implements \JsonSerializable
{
    #[ORM\Column(type: DoctrineTypes::STRING, length: 255, nullable: false, options: ['default' => ''])]
    private string $name;

    #[ORM\Column(type: DoctrineTypes::STRING, length: 255, nullable: false, options: ['default' => ''])]
    private string $description;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'tenant', fetch: 'LAZY')]
    private Collection $users;

    public function __construct(string $name, ?string $description = '')
    {
        parent::__construct();

        $this->name = $name;
        $this->description = $description;

        $this->users = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
        ];
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setTenant($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getTenant() === $this) {
                $user->setTenant(null);
            }
        }

        return $this;
    }
}
