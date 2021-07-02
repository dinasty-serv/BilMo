<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProductFixtures extends Fixture
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
            $product = new Product();
            $product->setName("Produit ".$i);
            $product->setDescription('Description du produit '.$i);
            $product->setPrix(mt_rand(9, 560));

            $manager->persist($product);


    }
        $manager->flush();
    }
}
