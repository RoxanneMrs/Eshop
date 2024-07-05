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



    
    public function findProductsByCriteria(string $order,int $lastProductId = 0, int $categoryId = null, float $lastProductPrice = 0, int $limit = 8): array
    {
    $qb = $this->createQueryBuilder('p');


    if ($categoryId !== null && $categoryId !== 0) {
        $qb->andWhere('p.category = :categoryId')
            ->setParameter('categoryId', $categoryId);
    }

    if ($order === 'ASC') {
        $qb->andWhere('p.price > :lastProductPrice')
            ->andWhere('p.id != :lastProductId')
            ->setParameter('lastProductPrice', $lastProductPrice)
            ->setParameter('lastProductId', $lastProductId)
            ->orderBy('p.price', $order);
    } else if  ($order === 'DESC') {
        $qb->andWhere('p.price <= :lastProductPrice')
            ->andWhere('p.id != :lastProductId')
            ->setParameter('lastProductPrice', $lastProductPrice)
            ->setParameter('lastProductId', $lastProductId)
            ->orderBy('p.price', 'DESC');
    } else {
        $qb->andWhere('p.id > :lastProductId')
            ->setParameter('lastProductId', $lastProductId)
            ->orderBy('p.id', 'ASC');
    }

    $qb->setMaxResults($limit);

    return $qb->getQuery()->getResult();
}

}
