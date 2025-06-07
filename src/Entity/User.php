<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Interfaces\TenantScopedUserInterface;
use App\Entity\Interfaces\UserInterface;
use App\Enum\UserTypeEnum;
use App\Repository\UserRepository;
use App\Utils\Roles;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User extends AbstractBaseEntity implements UserInterface, \JsonSerializable, TenantScopedUserInterface
{
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING, unique: true)]
    private string $providerId = '';
    
    #[Assert\Email]
    #[ORM\Column(type: Types::STRING, length: 180, unique: true)]
    private string $email = '';

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING)]
    private string $fullName = '';

    #[ORM\Column(type: Types::STRING)]
    private string $provider = '';

    #[ORM\Column(type: Types::STRING, enumType: UserTypeEnum::class)]
    private UserTypeEnum $userType;

    #[ORM\ManyToOne(cascade: ['persist'], fetch: 'EAGER', inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private Tenant $tenant;

    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    public function __construct(string $email, Tenant $tenant)
    {
        parent::__construct();

        $this->email = $email;
        $this->tenant = $tenant;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function setFullName(string $fullName): void
    {
        $this->fullName = $fullName;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->providerId;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return $this->providerId;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getProvider(): ?string
    {
        return $this->provider;
    }

    public function setProvider(string $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    public function getUserType(): UserTypeEnum
    {
        return $this->userType;
    }

    public function setUserType(UserTypeEnum $userType): void
    {
        $this->userType = $userType;
    }

    public function getProviderId(): string
    {
        return $this->providerId;
    }

    public function setProviderId(string $providerId): void
    {
        $this->providerId = $providerId;
    }

    final public function jsonSerialize(): array
    {
        return [
            'fullname' => $this->getFullName(),
            'email' => $this->getEmail(),
            'type' => $this->getUserType()->value,
            'providerId' => $this->providerId,
            'tenant' => $this->getTenant(),
        ];
    }

    public function getBlamableIdentifier(): string
    {
        return $this->getEmail();
    }

    public function getTenant(): Tenant
    {
        return $this->tenant;
    }

    public function setTenant(Tenant $tenant): static
    {
        $this->tenant = $tenant;

        return $this;
    }
}
