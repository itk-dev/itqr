<?php

declare(strict_types=1);

namespace App\Security\TenantScope\Doctrine;

use App\Entity\Interfaces\SharedScopedEntityInterface;
use App\Entity\Interfaces\TenantScopedEntityInterface;
use App\Security\TenantScope\TenantScopeException;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

/**
 * Class TenantFilter.
 *
 * Doctrine filter to handle tenant scope. Ensures all doctrine queries are filtered by
 * users tenant.
 */
class TenantFilter extends SQLFilter
{
    /**
     * @throws TenantScopeException
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, string $targetTableAlias): string
    {
        if (!$this->hasParameter('tenant_id')) {
            return '';
        }

        // Check if the entity implements the shared interface
        if ($targetEntity->getReflectionClass()->implementsInterface(SharedScopedEntityInterface::class)) {
            return sprintf('(%s.tenant_id = %s OR %s.is_shared = true)',
                $targetTableAlias,
                $this->getParameter('tenant_id'),
                $targetTableAlias
            );
        }

        // Check if the entity implements the required interfaces
        if (!$targetEntity->getReflectionClass()->implementsInterface(TenantScopedEntityInterface::class)) {
            return '';
        }

        try {
            return sprintf('%s.tenant_id = %s', $targetTableAlias, $this->getParameter('tenant_id'));
        } catch (\Exception $e) {
            throw new TenantScopeException('Error applying tenant filter constraint: '.$e->getMessage(), $e->getCode(), $e);
        }
    }
}
