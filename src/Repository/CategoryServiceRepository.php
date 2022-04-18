<?php

namespace App\Repository;

use App\Entity\CategoryService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CategoryService|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryService|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryService[]    findAll()
 * @method CategoryService[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryService::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(CategoryService $entity, bool $flush = true): void
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
    public function remove(CategoryService $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findAllByDate()
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.updatedAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
    public function findAllByDateF()
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.updatedAt', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult()
        ;
    }


    // /**
    //  * @return CategoryService[] Returns an array of CategoryService objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CategoryService
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
