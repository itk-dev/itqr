<?php

namespace App\Controller\Admin;

use App\Entity\Interfaces\TenantScopedEntityInterface;
use App\Entity\Interfaces\TenantScopedUserInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

abstract class AbstractTenantAwareCrudController extends AbstractCrudController
{
    public function createEntity(string $entityFqcn): TenantScopedEntityInterface
    {
        $entity = parent::createEntity($entityFqcn);

        $this->setTenant($entity);

        return $entity;
    }

    /** @phpstan-ignore missingType.parameter */
    private function setTenant($entity): void
    {
        if ($entity instanceof TenantScopedEntityInterface) {
            $user = $this->getUser();

            assert($user instanceof TenantScopedUserInterface);

            $entity->setTenant($user->getTenant());
        }
    }
}
