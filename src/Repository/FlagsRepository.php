<?php

namespace App\Repository;

use App\Entity\Flags;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Flags|null find($id, $lockMode = null, $lockVersion = null)
 * @method Flags|null findOneBy(array $criteria, array $orderBy = null)
 * @method Flags[]    findAll()
 * @method Flags[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FlagsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Flags::class);
    }

    public function findFlagsHomePage($user)
    {
        return $this->createQueryBuilder('f')
        ->Select('(f.Category)')
        ->addSelect('MAX(f.Post)')
        ->Where('f.User = :val')
        ->groupBy('f.Category')
        ->orderBy('MAX(f.Post)')
        ->setParameter('val', $user)
        ->getQuery()
        ->getResult()
        ;
    }

    // /**
    //  * @return Flags[] Returns an array of Flags objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Flags
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
