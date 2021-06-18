<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ProductController extends AbstractController
{

    /**
     * @Route("/api/product/list", name="api_product_list", methods={"GET"})
     * @param ProductRepository $productRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function list(ProductRepository $productRepository, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse(
            $serializer->serialize($productRepository->findAll(), "json", ["groups" => "getlist"]),
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
