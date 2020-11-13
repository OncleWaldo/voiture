<?php

namespace App\Repository;

use App\Entity\Voiture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Voiture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Voiture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Voiture[]    findAll()
 * @method Voiture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoitureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Voiture::class);
    }

    // /**
    //  * @return Voiture[] Returns an array of Voiture objects
    //  */
    
    public function findByAvailable($datestart, $dateend)
    {
        $subQb = $this->getEntitymanager()->createQueryBuilder();

        $subQ = $this->createQueryBuilder('v')
            ->leftJoin('v.reservations', 'r')
            ->andWhere(':valstart BETWEEN r.dateStart AND r.dateEnd')
            ->orWhere(':valend BETWEEN r.dateStart AND r.dateEnd')
            ->setParameter('valstart', $datestart)
            ->setParameter('valend', $dateend);
        

        $notIN = $this->createQueryBuilder('c')
            ->where($subQb->expr()->notIn('c.id', $subQ->getDQL()))
            ->andWhere('c.bookable = :bookable')
            ->setParameter('valstart', $datestart)
            ->setParameter('valend', $dateend)
            ->setParameter('bookable', true)
            ->getQuery()
            ->getResult()
        ;
        return $notIN;
    }
    

    /*
    public function findOneBySomeField($value): ?Voiture
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
