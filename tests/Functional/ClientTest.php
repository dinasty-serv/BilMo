<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ClientTest extends WebTestCase
{

    private static function getClient(){
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/login_check',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode(
                array(
                    'username' => "admin",
                    'password' => "0000",
                )
            )
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }

    /**
     * @Description Test create user invalid email
     * @return void
     * @author Nicolas de Fontaine
     */
    public function testCreateUserInvalidEmail(){
        $client = self::getClient();

        $client->request(
            'POST',
            'api/user',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'),
           json_encode([

               'email' => "Test ajout",
               "username" => "TestAjout"

           ])
        );

        $this->assertEquals('400', $client->getResponse()->getStatusCode());

    }

    /**
     * @Description Test create user
     * @return void
     * @author Nicolas de Fontaine
     */
    public function testCreateUser(){
        $client = self::getClient();

        $client->request(
            'POST',
            'api/user',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'),
            json_encode([

                'email' => "testajodddddutth@email.com",
                "username" => "TestAjout"

            ])
        );

        $this->assertEquals('201', $client->getResponse()->getStatusCode());

    }

    /**
     * @Description Test update
     * @return void
     * @author Nicolas de Fontaine
     */
    public function testUpdateUser(){
        $client = self::getClient();
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user= $em->getRepository(User::class)->findOneBy(array(), null, $limit = 1, $offset = null);

        $client->request(
            'PUT',
            'api/user/'.$user->getId(),
            array("id" => $user->getId()),
            array(),
            array('CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'),
            json_encode([

                'email' => "testUpdate@email.com",
                "username" => "TestUpdate"

            ])
        );
        $this->assertEquals('200', $client->getResponse()->getStatusCode());
    }

    /**
     * @Description Test delete user
     * @return void
     * @author Nicolas de Fontaine
     */
    public function testDeleteUser(){
        $client = self::getClient();
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user= $em->getRepository(User::class)->findOneBy(array(), null, $limit = 1, $offset = null);

        $client->request(
            'DELETE',
            'api/user/'.$user->getId(),
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'),
            json_encode([

                'email' => "testUpdate@email.com",
                "username" => "TestUpdate"

            ])
        );
        $this->assertEquals('204', $client->getResponse()->getStatusCode());
    }
}