<?php

declare(strict_types=1);

namespace App\Security\TenantScope;

use App\Entity\Interfaces\TenantScopedEntityInterface;
use App\Entity\User;
use App\Repository\TenantRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * PrePersistListener Class.
 *
 * Doctrine event listener to set tenant of all new entities from the Users active tenant.
 */
#[AsDoctrineListener(event: Events::prePersist)]
readonly class PrePersistListener
{
    public function __construct(
        // private Security $security,
        private TenantRepository $tenantRepository,
    ) {
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $this->setTenant($args->getObject());
    }

    /**
     * Set entity tenant from users active tenant.
     */
    private function setTenant(object $object): void
    {
        if (!$object instanceof TenantScopedEntityInterface) {
            return;
        }

        $all = $this->tenantRepository->findAll();
        $object->setTenant($all[0]);

        //        @TODO Enable when OIDC setup complete
        //        $user = $this->security->getUser();
        //
        //        if ($user instanceof User) {
        //            $object->setTenant($user->getActiveTenant());
        //        }
    }
}
