<?php

namespace App\Repository;

use App\Entity\Comments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comments|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comments|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comments[]    findAll()
 * @method Comments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comments::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Comments $entity, bool $flush = true): void
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
    public function remove(Comments $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findAllByDate(){
        return $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Comments[] Returns an array of Comments objects
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

    /**
     * @return Comments[] Returns an array of Comments objects
     */
    
    public function findByAllComment($id)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.isPublished = :isPublished')
            ->setParameter('isPublished', 1)
            ->andWhere("c.article= :id")
            ->setParameter(':id',$id)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    // public function findAllCommentIspublished(){
    //     return $this->createQueryBuilder('a')
    //         ->andWhere('a.isPublished = :val')
    //         ->setParameter('val', 1)
    //         ->orderBy('a.createdAt', 'DESC')
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }

    /*
    public function findOneBySomeField($value): ?Comments
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
