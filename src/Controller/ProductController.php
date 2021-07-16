<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Open API
 */
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

class ProductController extends AbstractController
{

    /**
     * @Route("/api/products/{page}", name="api_product_list", methods={"GET"}, defaults={"page"=null})
     * @param int|null $page
     * @param ProductRepository $productRepository
     * @param SerializerInterface $serializer
     *
     * @return JsonResponse
     *      * @OA\Response(
     *     response=200,
     *     description="Returns the products list",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Product::class, groups={"getlist"}))
     *     )
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="Page",
     *     @OA\Schema(type="int")
     * )
     * @OA\Tag(name="Products")
     * @Security(name="Bearer")
     *
     *
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
     * @Route("/api/products/{id}",
     *     name="api_product_detail",
     *     methods={"GET"},
     *     requirements={"id"="\d+"})
     *
     * @return JsonResponse
     *      * @OA\Response(
     *     response=200,
     *     description="Returns the product details",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Product::class, groups={"get"}))
     *     )
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="query",
     *     description="Product ID",
     *     @OA\Schema(type="int")
     * )
     * @OA\Tag(name="Products")
     * @Security(name="Bearer")
     */
    public function item(Product $product, SerializerInterface $serializer):Response
    {
        return new JsonResponse(
            $serializer->serialize($product, "json", ["groups" => "get"]),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }
}
