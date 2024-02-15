<?php

namespace App\Repository;

use App\Entity\ModeleVehicule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ModeleVehicule>
 *
 * @method ModeleVehicule|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModeleVehicule|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModeleVehicule[]    findAll()
 * @method ModeleVehicule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModeleVehiculeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModeleVehicule::class);
    }

//    /**
//     * @return ModeleVehicule[] Returns an array of ModeleVehicule objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ModeleVehicule
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
