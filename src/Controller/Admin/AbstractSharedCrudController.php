<?php

namespace App\Controller\Admin;

use App\Entity\Interfaces\TenantScopedEntityInterface;
use App\Entity\Interfaces\TenantScopedUserInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

abstract class AbstractSharedCrudController extends AbstractCrudController
{
    public function createEntity(string $entityFqcn): TenantScopedEntityInterface
    {
        $entity = parent::createEntity($entityFqcn);


        $this->setTenant($entity);

        return $entity;
    }

    /** @phpstan-ignore missingType.parameter */
    protected function setTenant($entity): void
    {
        if ($entity instanceof TenantScopedEntityInterface) {
            $user = $this->getUser();

            assert($user instanceof TenantScopedUserInterface);

            $entity->setTenant($user->getTenant());
        }
    }
}
