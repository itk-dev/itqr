<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Entity\Interfaces\GlobalScopedEntityInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
abstract class AbstractGlobalScopedEntity extends AbstractTenantScopedEntity implements GlobalScopedEntityInterface
{
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isShared = false;

    public function isShared(): bool
    {
        return $this->isShared;
    }

    public function setIsShared(bool $isShared): static
    {
        $this->isShared = $isShared;

        return $this;
    }
}
