<?php

namespace App\Repository;

use App\Entity\Livre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Livre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Livre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Livre[]    findAll()
 * @method Livre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livre::class);
    }

    // /**
    //  * @return Livre[] Returns an array of Livre objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Livre
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function queryAction13($date1,$date2): array
    {   
        return $this->createQueryBuilder('l')
                    ->Where('l.date_de_parution >= :date1')
                    ->setParameter('date1', $date1)
                    ->andWhere('l.date_de_parution <= :date2')
                    ->setParameter('date2', $date2)
                    ->getQuery()
                    ->getResult();
    }

    public function queryAction15($date1,$date2,$note1,$note2): array
    {   
        return $this->createQueryBuilder('l')
                    ->Where('l.date_de_parution >= :date1')
                    ->setParameter('date1', $date1)
                    ->andWhere('l.date_de_parution <= :date2')
                    ->setParameter('date2', $date2)
                    ->andWhere('l.note >= :note1')
                    ->setParameter('note1', $note1)
                    ->andWhere('l.note <= :note2')
                    ->setParameter('note2', $date2)
                    ->getQuery()
                    ->getResult();
    }

    public function queryAction25($titre): array
    {   
        return $this->createQueryBuilder('l')
                    ->select('l.titre')
                    ->Where('l.titre LIKE :titre')
                    ->setParameter('titre', '%'.$titre.'%')
                    ->getQuery()
                    ->getResult();
    }

    public function queryAction26($new_note,$id)
    {   
        $query = $this->getEntityManager()
                        ->createQuery("UPDATE App\Entity\Livre l 
                                        SET l.note = l.note + :new_note  WHERE l.id = :id")
                        ->setParameter('new_note', $new_note)
                        ->setParameter('id', $id);
        return $query->execute();
    }


    //FAIT PAR AXEL

    public function Reqaction20()
    {
        $querybuilder = $this->createQueryBuilder('l')
             ->orderBy('l.date_de_parution','asc')
        ;

        return $querybuilder->getQuery()->getResult();
    }
}
