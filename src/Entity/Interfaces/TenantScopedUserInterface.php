<?php

declare(strict_types=1);

namespace App\Entity\Interfaces;

use App\Entity\Tenant;
use Doctrine\Common\Collections\Collection;

interface TenantScopedUserInterface
{
    public function getTenant(): Tenant;

    public function setTenant(Tenant $tenant): self;
}
