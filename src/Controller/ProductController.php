<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ProductController extends AbstractController
{

    /**
     * @Route("/api/products/{page}", name="api_product_list", methods={"GET"}, defaults={"page"=null})
     * @param int|null $page
     * @param ProductRepository $productRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function list(
        int $page = null,
        ProductRepository $productRepository,
        SerializerInterface $serializer

    ): JsonResponse
    {
        $max = 10;

        /**
         * Fix number page default
         */
        if (!$page){
            $page = 1;
        }else{
            $page = $page +1;
        }

        $products = $productRepository->createQueryBuilder('a')
            ->setFirstResult(($page*$max)-$max)
            ->setMaxResults($max)
        ->orderBy('a.id', 'ASC');

        return new JsonResponse(
            $serializer->serialize($products->getQuery()->getResult(), "json", ["groups" => "getlist"]),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }

    /**
     * @Route("/api/product/{id}",
     *     name="api_product_detail",
     *     methods={"GET"},
     *     requirements={"id"="\d+"})
     */
    public function product(Product $product, SerializerInterface $serializer):Response
    {
        return new JsonResponse(
            $serializer->serialize($product, "json", ["groups" => "get"]),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }
}
