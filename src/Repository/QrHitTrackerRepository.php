<?php

namespace App\Repository;

use App\Entity\QrHitTracker;
use App\Entity\Tenant\Qr;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QrHitTracker>
 */
class QrHitTrackerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QrHitTracker::class);
    }

    public function getHitCount(Qr $qr): int
    {
        return $this->count(['qr' => $qr]);
    }
}
