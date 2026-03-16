<?php

namespace App\Repository;

use App\Entity\Livre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;



/**
 * @extends ServiceEntityRepository<Livre>
 */
class LivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, livre::class);
    }




    public function paginatelivres(int $page): PaginationInterface 
    {

        return $this->paginator->paginate(  
            $this->createQueryBuilder('l')->leftJoin('l.category', 'c')->select('l', 'c'), 
            $page,  
            4,    
            [
                'distinct' => false,   
                'sortFieldAllowList' => ['l.id', 'l.title'] 
                
            ]
        );

        /*
        $builder = $this->createQueryBuilder('l')->leftJoin('l.category', 'c')->select('l', 'c');
        if($userId) {
            $builder = $builder->andWhere('l.user = :user')
                ->setParameter('user', $userId);
        }
        return $this->paginator->paginate(
            $builder,
            $page,
            2,
            [
                'distinct' => false,
                'sortFieldAllowList' => ['l.id', 'l.title']
            ]

            );

            */




    }

    //    /**
//     * @return Livre[] Returns an array of Livre objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    //    public function findOneBySomeField($value): ?Livre
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
