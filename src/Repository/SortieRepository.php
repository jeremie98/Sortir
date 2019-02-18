<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function findSortiesContenant(string $search){
        return $this->createQueryBuilder('s')
            ->andWhere('s.nom LIKE :search')
            ->setParameter('search', '%'.$search.'%')
            ->orderBy('s.dateSortie', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findSortiesPlusRecentes()
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.dateSortie', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findSortiesEntreDates(DateTime $dateEntre, DateTime $dateEt){
        return $this->createQueryBuilder('s')
            ->andWhere('s.dateSortie BETWEEN :dateEntre and :dateEt')
            ->setParameter('dateEntre', $dateEntre)
            ->setParameter('dateEt', $dateEt)
            ->orderBy('s.dateSortie', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findSortiesPass()
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.dateSortie < :date')
            ->setParameter('date', new \DateTime(date('Y-m-d H:i:s')))
            ->orderBy('s.dateSortie', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

}
