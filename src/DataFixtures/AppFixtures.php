<?php

namespace App\DataFixtures;

use App\Entity\Tenant;
use App\Entity\Tenant\Qr;
use App\Entity\Tenant\Url;
use App\Enum\QrModeEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Create a tenant entity
        $tenant = new Tenant(); // Replace Tenant with the actual class
        $tenant->setTitle('Fixture tenant');
        $tenant->setDescription('Fixture tenant description');
        $tenant->setCreatedAt(new \DateTimeImmutable());
        $tenant->setCreatedBy('fixture_author');
        $tenant->setModifiedAt(new \DateTimeImmutable());
        $tenant->setModifiedBy('fixture_author');
        $manager->persist($tenant);

        $departments = [
            'Department A',
            'Department B',
        ];

        // Create Qr entities
        for ($i = 0; $i < 80; ++$i) {
            $qr = new Qr();
            $qr->setTitle('qr '.$i);
            $qr->setDepartment(0 == $i % 2 ? $departments[0] : $departments[1]);
            $qr->setAuthor('fixture_author');
            $qr->setMode(QrModeEnum::DEFAULT);
            $qr->setTenant($tenant);
            $qr->setCreatedAt(new \DateTimeImmutable());
            $qr->setCreatedBy('fixture_author');
            $qr->setModifiedAt(new \DateTimeImmutable());
            $qr->setModifiedBy('fixture_author');

            $manager->persist($qr);

            $url = new Url();
            $url->setUrl('http://localhost/loremipsum/long_url/'.$i);
            $url->setQr($qr);
            $url->setTenant($tenant);
            $url->setCreatedAt(new \DateTimeImmutable());
            $url->setCreatedBy('fixture_author');
            $url->setModifiedAt(new \DateTimeImmutable());
            $url->setModifiedBy('fixture_author');

            $manager->persist($url);
        }

        $manager->flush();
    }
}
