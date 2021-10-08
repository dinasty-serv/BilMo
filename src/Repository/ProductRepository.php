<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;

/**
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

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function getProductsByPage($page,$limit = 10){
        $max = $limit;
        $products =  $this->createQueryBuilder('a')
            ->setFirstResult(($page*$max)-$max)
            ->setMaxResults($max)
            ->orderBy('a.id', 'ASC')->getQuery()->getResult();

        $totalPage = $this->count([]) / $limit;

        return new PaginatedRepresentation(
            new CollectionRepresentation($products),
            'api_product_list', // route
            array(), // route parameters
            $page,       // page number
            $limit,      // limit
            $totalPage,       // total pages
            'page',  // page route parameter name, optional, defaults to 'page'
            'limit', // limit route parameter name, optional, defaults to 'limit'
            true  // generate relative URIs, optional, defaults to `false`
        );
    }
}
