<?php

namespace App\Tests\Functional;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\AbstractBrowser;


class ProductTest extends WebTestCase
{

    /**
     * @Description return client login
     * @return KernelBrowser|AbstractBrowser|null
     * @author Nicolas de Fontaine
     */
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
     * @Description Test access product list not login
     * @return void
     * @author Nicolas de Fontaine
     */
    public function testProductsListNotLogin(){

        $client = static::createClient();
        $client->request(
            'GET',
            '/api/products',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
        );

        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals('401', $response['code']);


    }

    /**
     * @Description Test access product list login
     * @return void
     * @author Nicolas de Fontaine
     */
    public function testProductsListLogin(){


        $client = self::getClient();

        $client->request(
            'GET',
            'api/products',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json', 'ACCEPT' => 'application/json'),
            null
        );
        $this->assertEquals('200', $client->getResponse()->getStatusCode());
    }

    /**
     * @Description Test access product detail not login
     * @return void
     * @author Nicolas de Fontaine
     */
    public function testProductDetailNotLogin(){
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');

        $product= $em->getRepository(Product::class)->findOneBy(array(), null, $limit = 1, $offset = null);

        $client->request('GET', '/api/products/' . $product->getId());

        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * @Description Test access product detail login
     * @return void
     * @author Nicolas de Fontaine
     */
    public function testProductDetailLogin(){
        $client = self::getClient();
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');

        $product= $em->getRepository(Product::class)->findOneBy(array(), null, $limit = 1, $offset = null);

        $client->request('GET', '/api/products/' . $product->getId());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}