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
        // Check if the tenant filter parameter is set
        if (!$this->hasParameter('tenant_id')) {
            return '';
        }

        try {
            // Check if the entity implements the shared interface
            if ($targetEntity->getReflectionClass()->implementsInterface(SharedScopedEntityInterface::class)) {
                return sprintf('(%s.tenant_id = %s OR %s.is_shared = true)',
                    $targetTableAlias,
                    $this->getParameter('tenant_id'),
                    $targetTableAlias
                );
            }

            // Check if the entity implements the tenant interface
            if ($targetEntity->getReflectionClass()->implementsInterface(TenantScopedEntityInterface::class)) {
                return sprintf('%s.tenant_id = %s',
                    $targetTableAlias,
                    $this->getParameter('tenant_id'));
            }

            // If the entity does not implement either of the tenant interfaces, return an empty string
            return '';
        } catch (\Exception $e) {
            throw new TenantScopeException('Error applying tenant filter constraint: '.$e->getMessage(), $e->getCode(), $e);
        }
    }
}
