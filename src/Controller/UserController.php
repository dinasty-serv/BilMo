<?php


namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
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
     *
     * @return Response
     *     * @return JsonResponse
     *      * @OA\Response(
     *     response=200,
     *     description="Returns the list users",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"get"}))
     *     )
     * )
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     */

    public function list(UserRepository $userRepository, SerializerInterface $serializer): Response
    {

        return new JsonResponse(
            $serializer->serialize($userRepository->findBy(['client' => $this->getUser()]), "json", ["groups" => "getlist"]),
            JsonResponse::HTTP_OK,
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
     *     * @return JsonResponse
     *      * @OA\Response(
     *     response=200,
     *     description="Return the user détails",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"get"}))
     *     )
     * )
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     */
    public function item(User $user, SerializerInterface $serializer): JsonResponse
    {
        $this->denyAccessUnlessGranted('check', $user);

        return new JsonResponse(
            $serializer->serialize($user, "json", ["groups" => "get"]),
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
     *      *@OA\Response(
     *     response=200,
     *     description="Put data into User object",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"getlist"}))
     *     )
     * )
     * @OA\Parameter(
     *     name="user",
     *     in="query",
     *     description="User ID",
     *     @OA\Schema(type="int")
     * )
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     *
     *
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

        $data = $serializer->deserialize($request->getContent(), User::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $user]);

        $errors = $validator->validate($data);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $entityManager->persist($user);
        $entityManager->flush();
        return new JsonResponse(
            $serializer->serialize($user, "json", ["groups" => "get"]),
            JsonResponse::HTTP_NO_CONTENT,
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
     *
     *  * @return JsonResponse
     *      *@OA\Response(
     *     response=200,
     *     description="Return User object created",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"get"}))
     *     )
     *
     * )
     *
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     *
     *
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
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(
            $serializer->serialize($user, "json", ["groups" => "get"]),
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
     *      *@OA\Response(
     *     response=200,
     *     description="No content",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"getlist"}))
     *     )
     * )
     * @OA\Parameter(
     *     name="user",
     *     in="query",
     *     description="User ID",
     *     @OA\Schema(type="int")
     * )
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     *
     *
     */
    public function delete(
        User $user,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $this->denyAccessUnlessGranted('check', $user);
        $entityManager->remove($user);
        $entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}