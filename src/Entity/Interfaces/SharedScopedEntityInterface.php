<?php

declare(strict_types=1);

namespace App\Entity\Interfaces;

use App\Entity\Tenant;

interface SharedScopedEntityInterface
{
    public function getIsShared(): bool;

    public function setIsShared(bool $isShared): self;
}
