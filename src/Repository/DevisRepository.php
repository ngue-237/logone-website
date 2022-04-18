<?php

namespace App\Repository;

use App\Entity\Devis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Devis|null find($id, $lockMode = null, $lockVersion = null)
 * @method Devis|null findOneBy(array $criteria, array $orderBy = null)
 * @method Devis[]    findAll()
 * @method Devis[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DevisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Devis::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Devis $entity, bool $flush = true): void
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
    public function remove(Devis $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return Devis[] Returns an array of Devis objects
     */
    
    public function findAllOderDesc()
    {
        return $this->createQueryBuilder('d')
            ->orderBy('d.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
    
    /**
     * @return Devis[] Returns an array of Devis objects
     */
    
    public function findAllAccepted()
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.closingStatus = :isClosed')
            ->setParameter('isClosed', 1)
            ->getQuery()
            ->getResult()
        ;
    }
    public function findAllDevisByCategoryService($idCategoryService)
    {
        
        return $this->createQueryBuilder('d')
            ->andWhere('d.categories = :idCategoryService')
            ->setParameter('idCategoryService', $idCategoryService)
            ->getQuery()
            ->getResult()
        ;
    }
    /**
     * @return Devis[] Returns an array of Devis objects
     */
    
    public function findAllJobDone()
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.jobDone = :isDone')
            ->setParameter('isDone', 1)
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Devis
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
