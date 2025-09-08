<?php

declare(strict_types=1);

namespace App\Entity\Tenant;

use App\Entity\Interfaces\SharedScopedEntityInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
abstract class AbstractSharedScopedEntity extends AbstractTenantScopedEntity implements SharedScopedEntityInterface
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
