<?php

namespace App\Controller\Admin;

use App\Entity\Interfaces\TenantScopedEntityInterface;
use App\Repository\TenantRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

abstract class AbstractTenantAwareCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly TenantRepository $tenantRepository,
    ) {
    }

    public function createEntity(string $entityFqcn): TenantScopedEntityInterface
    {
        $entity = parent::createEntity($entityFqcn);

        //        @TODO Enable when OIDC setup complete
        //        $user = $this->getUser();
        //        $entity->setTenant($user->getActiveTenant());

        $all = $this->tenantRepository->findAll();
        if (!empty($all)) {
            $entity->setTenant($all[0]);
        }

        return $entity;
    }
}
