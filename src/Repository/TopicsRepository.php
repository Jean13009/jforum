<?php

namespace App\Repository;

use App\Entity\Topics;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
* @method Topics|null find($id, $lockMode = null, $lockVersion = null)
* @method Topics|null findOneBy(array $criteria, array $orderBy = null)
* @method Topics[]    findAll()
* @method Topics[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
*/
class TopicsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Topics::class);
    }
    
    public function findTopicsByLastpost($category, $limit, $offset)
    {
        return $this->createQueryBuilder('t')
        ->Where('t.category = :val')
        ->andWhere('pt.deleted = :val2')
        ->Join('t.posts', 'pt')
        ->groupBy('t.id')
        ->orderBy('MAX(pt.id)', 'DESC')
        ->setParameter('val', $category)
        ->setParameter('val2', false)
        ->setFirstResult($offset)
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult()
        ;
    }
    
    // /**
    //  * @return Topics[] Returns an array of Topics objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
        ->andWhere('t.exampleField = :val')
        ->setParameter('val', $value)
        ->orderBy('t.id', 'ASC')
        ->setMaxResults(10)
        ->getQuery()
        ->getResult()
        ;
    }
    */
    
    /*
    public function findOneBySomeField($value): ?Topics
    {
        return $this->createQueryBuilder('t')
        ->andWhere('t.exampleField = :val')
        ->setParameter('val', $value)
        ->getQuery()
        ->getOneOrNullResult()
        ;
    }
    */
    
}
