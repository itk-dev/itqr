<?php

namespace App\DataFixtures;

use App\Entity\AbstractBaseEntity;
use App\Entity\Tenant;
use App\Entity\Tenant\Qr;
use App\Entity\Tenant\Url;
use App\Entity\User;
use App\Entity\UserRoleTenant;
use App\Enum\QrModeEnum;
use App\Enum\UserTypeEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Create Tenants and Users
        $tenantAbc = new Tenant();
        $tenantAbc->setTenantKey('ABC');
        $tenantAbc->setTitle('Tenant ABC');
        $this->setCreatedModified($tenantAbc);
        $manager->persist($tenantAbc);

        $userA = $this->createUser('User A', $tenantAbc);
        $manager->persist($userA);

        $userB = $this->createUser('User B', $tenantAbc);
        $manager->persist($userB);

        $userC = $this->createUser('User C', $tenantAbc);
        $manager->persist($userC);

        $tenantDef = new Tenant();
        $tenantDef->setTenantKey('DEF');
        $tenantDef->setTitle('Tenant DEF');
        $this->setCreatedModified($tenantDef);
        $manager->persist($tenantDef);

        $userD = $this->createUser('User D', $tenantDef);
        $manager->persist($userD);

        $userE = $this->createUser('User E', $tenantDef);
        $manager->persist($userE);

        $userF = $this->createUser('User F', $tenantDef);
        $manager->persist($userF);

        $tenants = [$tenantAbc, $tenantDef];

        // Create Qr entities
        for ($i = 0; $i < 80; ++$i) {
            $qr = new Qr();
            $qr->setTitle('qr '.$i);
            $qr->setTenant(0 == $i % 2 ? $tenants['0'] : $tenants['1']);
            $qr->setMode(QrModeEnum::DEFAULT);
            $this->setCreatedModified($qr);

            $manager->persist($qr);

            $url = new Url();
            $url->setTenant(0 == $i % 2 ? $tenants['0'] : $tenants['1']);
            $url->setUrl('http://localhost/loremipsum/long_url/'.$i);
            $url->setQr($qr);
            $this->setCreatedModified($url);

            $manager->persist($url);
        }

        $manager->flush();
    }

    private function createUser(string $name, Tenant $tenant): User
    {
        $slugger = new AsciiSlugger();
        $email = $slugger->slug($name)->lower()->toString().'@example.com';

        $user = new User();
        $user->setFullName($name);
        $user->setEmail($email);
        $user->setProviderId($email);
        $user->setUserType(UserTypeEnum::USERNAME_PASSWORD);

        $userRoleTenant = new UserRoleTenant($user, $tenant);
        $this->setCreatedModified($userRoleTenant);
        $user->addUserRoleTenant($userRoleTenant);

        $this->setCreatedModified($user);

        return $user;
    }

    private function setCreatedModified(AbstractBaseEntity $entity): void
    {
        $entity->setCreatedAt(new \DateTimeImmutable());
        $entity->setModifiedAt(new \DateTimeImmutable());
        $entity->setCreatedBy('app_fixtures');
        $entity->setModifiedBy('app_fixtures');
    }
}
