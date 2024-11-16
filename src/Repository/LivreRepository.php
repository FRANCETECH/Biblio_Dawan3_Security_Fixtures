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


    
    
    public function paginatelivres(int $page): PaginationInterface // On replace le type de retour par PaginationInterface qui est iterable
    {
        
        return $this->paginator->paginate(  // Cette méthode prend en premier param la cible qui n'a pas de type défini
            $this->createQueryBuilder('l')->leftJoin('l.category', 'c')->select('l', 'c'),  // premier parametre
            $page,  // deuxieme parametre la page
            4,     // troixieme parametre: la limite, combien d'élément par page   !!!!!!!!!!!!!
            [
                'distinct' => false,    // Comparer les requêtes avec distinct à true et à false, plus de performance à false
                'sortFieldAllowList' => ['l.id', 'l.title'] // Je lui précise que je n'autorise que les deux réorganisations
                // Pour tester on enlève r.title et voir s'il va accepter la réorg par r.title, puis on le remet
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



    /**
     * Calcule et retourne la somme totale de toutes les valeurs du champ publicationYear de l'entité.
     *
     * @return int La somme totale des années de publication.
     */
    public function findTotalYear(): int
    {
        // Création d'un QueryBuilder pour construire une requête sur l'entité associée au repository, avec l'alias 'l'.
        return $this->createQueryBuilder('l')
            // Sélectionne la somme de la colonne publicationYear et l'alias 'total' pour le résultat.
            ->select('SUM(l.publicationYear) as total')
            // Exécute la requête et récupère le résultat en tant que valeur scalaire unique (un entier dans ce cas).
            ->getQuery()
            ->getSingleScalarResult();
    }



    /**
     * Trouve et retourne les enregistrements dont l'année de publication est inférieure ou égale à une valeur donnée.
     *
     * @param int $publicationYear L'année de publication maximale à comparer.
     * @return array Un tableau contenant les résultats correspondant aux critères de la requête.
     */
    public function findWithPublicationYearLowerThan(int $publicationYear): array
    {
        // Création d'un QueryBuilder pour construire une requête sur l'entité associée au repository.
        return $this->createQueryBuilder('l')
            // Ajoute une condition "WHERE" pour sélectionner les enregistrements dont l'année de publication
            // est inférieure ou égale à la valeur fournie en paramètre.
            ->where('l.publicationYear <= :publicationYear')
            // Trie les résultats par année de publication en ordre croissant (ASC).
            ->orderBy('l.publicationYear', 'ASC')
            // Limite les résultats de la requête à un maximum de 10 enregistrements.
            ->setMaxResults(10)
            // Définit la valeur du paramètre ":publicationYear" utilisé dans la clause "WHERE".
            ->setParameter('publicationYear', $publicationYear)
            // Exécute la requête et récupère les résultats sous forme d'un tableau d'objets.
            ->getQuery()
            ->getResult();
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
