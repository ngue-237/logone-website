<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Article $entity, bool $flush = true): void
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
    public function remove(Article $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return Article[] Returns an array of Article objects
     */
    
    public function findAllByView()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.isPublished = :val')
            ->setParameter('val', 1)
            ->andWhere('a.isPublished = :val')
            ->setParameter('val', 1)
            ->orderBy('a.view', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllOderByDate()
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllByPublished($id)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.isPublished = :val')
            ->setParameter('val', 1)
            ->andWhere('a.categoryArticle= :id')
            ->setParameter('id', $id)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
    public function findAllByCategoryService($id)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.categoryService= :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult()
        ;
    }
    public function findAllByPub()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.isPublished = :val')
            ->setParameter('val', 1)
            ->getQuery()
            ->getResult()
        ;
    }
    

    // public function findByisPublished($val): ?Article
    // {
    //     return $this->createQueryBuilder('c')
    //         ->andWhere('c.isPublished = :isPublished')
    //         ->setParameter('isPublished', $val)
    //         ->orderBy('c.createdAt', 'DESC')
    //         ->setMaxResults(10)
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }
    
    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
