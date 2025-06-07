<?php

declare(strict_types=1);

namespace App\Security\TenantScope\Doctrine;

use App\Entity\Interfaces\TenantScopedUserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * class TenantFilterConfigurator
 *
 * Service to configure the TenantFilter with the tenant ID.
 */
readonly class TenantFilterConfigurator
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {
    }

    /**
     * Enable the tenant filter and set the tenant ID.
     */
    public function configureFilter(): void
    {
        $filter = $this->entityManager->getFilters()->enable('tenant_filter');

        if (!$filter instanceof TenantFilter) {
            return;
        }

        $user = $this->security->getUser();

        // If no user is logged in or the user doesn't implement TenantScopedUserInterface, don't set the tenant ID
        if (!$user instanceof TenantScopedUserInterface) {
            return;
        }

        $tenantId = $user->getTenant()->getId();

        $filter->setParameter('tenant_id', $tenantId);
    }
}
