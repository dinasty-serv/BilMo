<?php

namespace App\Repository;

use App\Entity\Client;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getUsersByPage($page,$limit = 10): PaginatedRepresentation
    {

        $max = 10;

        $users =  $this->createQueryBuilder('a')
            ->setFirstResult(($page*$max)-$max)
            ->setMaxResults($max)
            ->orderBy('a.id', 'ASC')->getQuery()->getResult();

        $totalPage = $this->count([]) / $limit;

        return new PaginatedRepresentation(
            new CollectionRepresentation($users),
            'api_users_list', // route
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
