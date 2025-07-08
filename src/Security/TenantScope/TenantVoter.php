<?php

namespace App\Security\TenantScope;

use App\Entity\Interfaces\TenantScopedEntityInterface;
use App\Entity\Interfaces\TenantScopedUserInterface;
use EasyCorp\Bundle\EasyAdminBundle\Security\Permission;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TenantVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return Permission::EA_EXECUTE_ACTION == $attribute
            && null !== $subject['entity']
            && $subject['entity']->getInstance() instanceof TenantScopedEntityInterface;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        assert($user instanceof TenantScopedUserInterface);
        $tenant = $user->getTenant();

        $entity = $subject['entity']->getInstance();

        return $entity->getTenant() === $tenant;
    }
}
