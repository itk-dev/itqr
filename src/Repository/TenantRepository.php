<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tenant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query\QueryException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tenant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tenant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tenant[]    findAll()
 * @method Tenant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TenantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tenant::class);
    }

    /**
     * Find Tenants from list of tenant keys. Return
     * collection indexed by tenant key.
     *
     * @throws QueryException
     */
    public function findByKeys(array $keys): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.tenantKey IN (:tenantKeys)')
            ->setParameter('tenantKeys', $keys)
            ->indexBy('t', 't.tenantKey')
            ->getQuery()
            ->getResult();
    }
}
