<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersFixtures extends Fixture
{
    /**
     * @var UserPasswordHasherInterface
     */
    private $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++){
            $user = new User();
            $user->setEmail("test".$i."@email.com");
            $user->setUsername('user'.$i);
            $clients = $manager->getRepository(Client::class)->findAll();
            foreach ($clients as $client){
                $user->setClient($client);

            }

            $manager->persist($user);


    }
        $manager->flush();
    }
}
