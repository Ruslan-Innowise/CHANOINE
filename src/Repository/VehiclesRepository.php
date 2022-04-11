<?php

namespace App\Repository;

use App\Entity\Vehicles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @method Vehicles|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vehicles|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vehicles[]    findAll()
 * @method Vehicles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VehiclesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicles::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Vehicles $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Vehicles $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return Vehicles[] Returns an array of Vehicles objects
    */
    public function findByAllFields(int $page, array $criteria, array $orderBy = null): array
    {
        $query =  $this->createQueryBuilder('v');

        foreach ($criteria as $key => $param) {
            $method = 'findBy' . $key . 'Field';
            $query = $this->$method($query, $param);
        }

        $query->orderBy( key($orderBy) ? 'v.' . key($orderBy) : 'v.id',  $orderBy[key($orderBy)] ?: 'ASC');

        $query->getQuery();

        $pageSize = 12;

        $paginator = new Paginator($query);

        $totalItems = count($paginator);

        $pageCount = ceil($totalItems / $pageSize);

        $paginator->getQuery()
            ->setFirstResult($pageSize * ($page-1)) // set the offset
            ->setMaxResults($pageSize)
            ->getResult(); // set the limit

        return ['items' => $paginator, 'totalItems' => $totalItems, 'pageCount' => (int)$pageCount];
    }

    private function findByBrandField($query, array $brand)
    {
        return $query->andWhere('v.brand IN (:brand)')
            ->setParameter('brand', $brand);
    }

    private function findByModelField($query, array $model)
    {
        return $query->andWhere('v.model IN (:model)')
            ->setParameter('model', $model);
    }

    private function findByPowerField($query, array $power)
    {
        return $query->andWhere('v.power IN (:power)')
            ->setParameter('power', $power);
    }

    private function findByPriceField($query, array $price)
    {
        return $query->andWhere('v.price BETWEEN :price_min AND :price_max')
            ->setParameter('price_min', $price[0])
            ->setParameter('price_max', $price[1]);
    }

    private function findByMonthlyField($query, array $price)
    {
        return $query->andWhere('v.priceMonthly BETWEEN :price_min AND :price_max')
            ->setParameter('price_min', $price[0])
            ->setParameter('price_max', $price[1]);
    }

    private function findByEnergyField($query, array $energy)
    {
        return $query->andWhere('v.energy IN (:energy)')
            ->setParameter('energy', $energy);
    }
}
