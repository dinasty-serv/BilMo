<?php


namespace App\Controller;


use App\Entity\User;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use JMS\Serializer\SerializerInterface as SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class UserController extends AbstractController
{
    /**
     * @Route("/api/users/",
     *     name="api_users_list",
     *     methods={"GET"})
     * @param UserRepository $userRepository
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     * @OA\Response(
     *     response=200,
     *     description="Returns the products list",
     *    @OA\JsonContent(
     *      @OA\Property(property="page",  description="Current page",  type="integer"),
     *      @OA\Property(property="limit", description="Limite items per page", type="integer"),
     *      @OA\Property(property="pages", description="number total page", type="integer"),
     *      @OA\Property(property="_links",
     *
     *               @OA\Property(property="self", @OA\Property(property="href", type="string")),
     *               @OA\Property(property="first", @OA\Property(property="href", type="string")),
     *               @OA\Property(property="last", @OA\Property(property="href", type="string")),
     *               @OA\Property(property="next", @OA\Property(property="href", type="string")),
     *           ),
     *      @OA\Property(property="_embedded",
     *      @OA\Property(property="items",type="array",
     *        @OA\Items(
     *               ref=@Model(type=User::class)
     *              )
     *          )
     *       )
     *     )
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
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     */

    public function list(UserRepository $userRepository,SerializerInterface $serializer, Request $request): Response
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
        $user = $this->getUser();


        $users = $userRepository->getUsersByPage($page, $limit, $user->getId());
        $json =  $serializer->serialize($users, "json");
        return new JsonResponse(
            $json,
            Response::HTTP_OK,
            [],
            true
        );

    }
    /**
     * @Route("/api/user/{id}",
     *     name="api_users_detail",
     *     methods={"GET"},
     *     requirements={"id"="\d+"})
     *
     * @param User $user
     * @param SerializerInterface $serializer
     * @return JsonResponse
     *
     * @return Response
     *
     *@OA\Response(
     *     response=200,
     *     description="Update User object",
     *     @OA\JsonContent(
     *               ref=@Model(type=User::class)
     *          )
     *      )
     * )
     *
     * @OA\Response(
     *     response=404,
     *     description="Not found",
     *
     *  @OA\JsonContent(
     *      @OA\Property(property="code", type="string"),
     *      @OA\Property(property="message",  type="string"),
     *        )
     *   )
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     */
    public function item(User $user, SerializerInterface $serializer): JsonResponse
    {
        $this->denyAccessUnlessGranted('check', $user);

        return new JsonResponse(
            $serializer->serialize($user, "json"),
            JsonResponse::HTTP_OK,
            [],
            true
        );

    }

    /**
     * @Route("/api/user/{id}", name="api_user_edit", methods={"PUT"})
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param UrlGeneratorInterface $urlGenerator
     * @param ValidatorInterface $validator
     *
     * @return JsonResponse
     * @OA\Response(
     *
     *     response=200,
     *     description="Update User object",
     *
     *      @OA\JsonContent(
     *               ref=@Model(type=User::class)
     *          )
     *        )
     *      )
     *
     *  @OA\RequestBody(
     *     request="Pet",
     *     description="Pet object that needs to be added to the store",
     *     required=true,
     *     @OA\JsonContent(
     *     ref=@Model(type=User::class, groups={"write_user"})),
     *
     * )
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     */
    public function put(
        User $user,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator
    ): JsonResponse {
        $this->denyAccessUnlessGranted('check', $user);

        $data = $serializer->deserialize($request->getContent(), User::class, 'json');

        $errors = $validator->validate($data);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), Response::HTTP_BAD_REQUEST, [], true);
        }

        $user->setUsername($data->getUsername());
        $user->setEmail($data->getEmail());

        $entityManager->persist($user);
        $entityManager->flush();
        return new JsonResponse(
            $serializer->serialize($user, "json"),
            Response::HTTP_OK,
            ["Location" => $urlGenerator->generate("api_users_detail", ["id" => $user->getId()])],
            true
        );
    }
    /**
     * @Route("/api/user", name="api_user_post", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param UrlGeneratorInterface $urlGenerator
     * @param ValidatorInterface $validator
     * @return JsonResponse
     * @OA\Response(
     *
     *     response=201,
     *     description="Create User object",
     *
     *       @OA\JsonContent(
     *               ref=@Model(type=User::class)
     *          )
     *        )
     *      )
     * @OA\RequestBody(
     *     request="Pet",
     *     description="Pet object that needs to be added to the store",
     *     required=true,
     *     @OA\JsonContent(ref=@Model(type=User::class, groups={"write_user"})),
     *
     * )
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     */
    public function post(
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator
    ): JsonResponse {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        $user->setClient($this->getUser());
        $errors = $validator->validate($user);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), Response::HTTP_BAD_REQUEST, [], true);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(
            $serializer->serialize($user, "json"),
            JsonResponse::HTTP_CREATED,
            ["Location" => $urlGenerator->generate("api_users_detail", ["id" => $user->getId()])],
            true
        );
    }

    /**
     * @Route("/api/user/{id}", name="api_user_delete", methods={"DELETE"})
     * @param User $user
     * @param EntityManagerInterface $entityManager
     *
     * @return JsonResponse
     *     @OA\Response(
     *     response=204,
     *     description="No content",
     *
     * )
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     */
    public function delete(
        User $user,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $this->denyAccessUnlessGranted('check', $user);
        $entityManager->remove($user);
        $entityManager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}