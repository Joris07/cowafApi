<?php

namespace App\Repository;

use App\Entity\DescriptionAnimal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DescriptionAnimal>
 *
 * @method DescriptionAnimal|null find($id, $lockMode = null, $lockVersion = null)
 * @method DescriptionAnimal|null findOneBy(array $criteria, array $orderBy = null)
 * @method DescriptionAnimal[]    findAll()
 * @method DescriptionAnimal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DescriptionAnimalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DescriptionAnimal::class);
    }

//    /**
//     * @return DescriptionAnimal[] Returns an array of DescriptionAnimal objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DescriptionAnimal
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
