<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface as SerializerInterface;

/**
 * Open API
 */
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

class ProductController extends AbstractController
{

    /**
     * @Route("/api/products", name="api_product_list", methods={"GET"})
     * @param ProductRepository $productRepository
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     *  @OA\Response(
     *     response=200,
     *     description="Returns the products list",
     *    @OA\JsonContent(
     *      @OA\Property(property="page",  description="Current page",  type="integer"),
     *      @OA\Property(property="limit",description="Limite items per page", type="integer"),
     *      @OA\Property(property="pages",description="number total page", type="integer"),
     *       @OA\Property(property="_links",
     *
     *               @OA\Property(property="self", @OA\Property(property="href", type="string")),
     *               @OA\Property(property="first", @OA\Property(property="href", type="string")),
     *               @OA\Property(property="last", @OA\Property(property="href", type="string")),
     *               @OA\Property(property="next", @OA\Property(property="href", type="string")),
     *           ),
     *      @OA\Property(property="_embedded",
     *      @OA\Property(property="items",type="array",
     *        @OA\Items(
     *               ref=@Model(type=Product::class))
     *          )
     *       )
     *     )
     *
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="Numéro de page",
     *     @OA\Schema(type="int")
     * )
     * @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Numbers of items",
     *     @OA\Schema(type="int")
     * )
     * @OA\Tag(name="Products")
     * @Security(name="Bearer")
     *
     */
    public function list(ProductRepository $productRepository, SerializerInterface $serializer, Request $request): JsonResponse
    {
        $page = $request->query->getInt('page');
        $limit = $request->query->getInt('limit');


        /**
         * Fix number page default
         */
        if (!$page){
            $page = 1;
        }
        if(!$limit){
            $limit = 10;
        }

        $products = $productRepository->getProductsByPage($page, $limit);



        return new JsonResponse(
            $serializer->serialize($products, "json"),
            Response::HTTP_OK,
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
     *     @Model(type=Product::class)
     * )
     *
     * @OA\Tag(name="Products")
     * @Security(name="Bearer")
     */
    public function item(Product $product, SerializerInterface $serializer):Response
    {

        return new JsonResponse(
            $serializer->serialize($product, "json"),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
