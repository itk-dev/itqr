<?php

namespace App\DataFixtures;

use App\Entity\Qr;
use App\Entity\Url;
use App\Enum\QrModeEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $departments = [
            'Department A',
            'Department B',
        ];
        // Create Qr entities
        for ($i = 0; $i < 80; ++$i) {
            $qr = new Qr();
            $qr->setTitle('qr '.$i);
            $qr->setDepartment(0 == $i % 2 ? $departments['0'] : $departments['1']);
            $qr->setAuthor('fixture_author');
            $qr->setMode(QrModeEnum::DEFAULT);

            $manager->persist($qr);

            $url = new Url();
            $url->setShortUri('http://localhost/qr/'.$i);
            $url->setUrl('http://localhost/loremipsum/long_url/'.$i);
            $url->setQr($qr);

            $manager->persist($url);
        }

        $manager->flush();
    }
}
