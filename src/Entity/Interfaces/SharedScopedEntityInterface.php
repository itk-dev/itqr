<?php

declare(strict_types=1);

namespace App\Entity\Interfaces;

interface SharedScopedEntityInterface
{
    public function getIsShared(): bool;

    public function setIsShared(bool $isShared): self;
}
