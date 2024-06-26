<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }


    public function findProductBySearch($search) : array {

        return $this->createQueryBuilder('a')

            ->andWhere('a.text LIKE :search')
            ->setParameter('search', "%" . $search . "%")
            ->getQuery()
            ->getResult()
        ;
    }

    public function findProductByFilter($filter) : array {

        return $this->createQueryBuilder('a')
            ->orderBy("a.price", $filter)
            ->getQuery()
            ->getResult()
        ;
    }
    

    // public function findProductByFilterPaginated(string $filter, int $offset, int $limit): array {

    //     $qb = $this->createQueryBuilder('a');

    //     if ($filter === 'desc') {
    //         $qb->orderBy('a.price', 'DESC');
    //     } else {
    //         $qb->orderBy('a.price', 'ASC');
    //     }
    
    //     $qb->setFirstResult($offset)
    //        ->setMaxResults($limit);  


    //     return $qb->getQuery()->getResult();
    // }
    
}
