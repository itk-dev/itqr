<?php

namespace App\Repository;

use App\Entity\Tenant\QrVisualConfig;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QrVisualConfig>
 * @method QrVisualConfig|null findOneBy(array $criteria, array $orderBy = null)
 */
class QrVisualConfigRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QrVisualConfig::class);
    }
}
