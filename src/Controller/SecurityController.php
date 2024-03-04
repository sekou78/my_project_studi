<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
// use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api', name: 'app_api_')]
class SecurityController extends AbstractController
{
    //VIDEO
    // public function __construct(
    //     private EntityManagerInterface $manager,
    //     private SerializerInterface $serializer
    // ) {
    // }

    // #[Route('/registration', name: 'registration', methods: 'POST')]
    // /** @OA\Post(
    //  *     path="/api/registration",
    //  *     summary="Inscription d'un nouvel utilisateur",
    //  *     @OA\RequestBody(
    //  *         required=true,
    //  *         description="Données de l'utilisateur à inscrire",
    //  *         @OA\JsonContent(
    //  *             type="object",
    //  *             @OA\Property(property="email", type="string", example="adresse@email.com"),
    //  *             @OA\Property(property="password", type="string", example="Mot de passe")
    //  *         )
    //  *     ),
    //  *     @OA\Response(
    //  *         response=201,
    //  *         description="Utilisateur inscrit avec succès",
    //  *         @OA\JsonContent(
    //  *             type="object",
    //  *             @OA\Property(property="user", type="string", example="Nom d'utilisateur"),
    //  *             @OA\Property(property="apiToken", type="string", example="31a023e212f116124a36af14ea0c1c3806eb9378"),
    //  *             @OA\Property(property="roles", type="array", @OA\Items(type="string", example="ROLE_USER"))
    //  *         )
    //  *     )
    //  * )
    //  */
    // public function register(
    //     Request $request,
    //     UserPasswordHasherInterface $passwordHasher
    // ): JsonResponse {
    //     $user = $this->serializer->deserialize(
    //         $request->getContent(),
    //         // type:
    //         User::class,
    //         // format:
    //         'json'
    //     );
    //     $user->setPassword(
    //         $passwordHasher->hashPassword($user, $user->getPassword)
    //     );
    //     $user->setCreatedAt(new \DateTimeImmutable());

    //     $this->manager->persist($user);
    //     $this->manager->flush();

    //     return new JsonResponse(
    //         [
    //             'user' => $user->getUserIdentifier(),
    //             'apiToken' => $user->getApiToken(),
    //             'roles' => $user->getRoles()
    //         ],
    //         // status:
    //         Response::HTTP_CREATED
    //     );
    // }
    //    //Route login
    // #[Route('/login', name: 'login', methods: 'POST')]
    // public function login(#[CurrentUser] ?User $user): JsonResponse
    // {
    //     if (null === $user) {
    //         return new JsonResponse(
    //             ['message' => 'missing credentials'],
    //             //status:
    //             Response::HTTP_UNAUTHORIZED
    //         );
    //     }

    //     return new JsonResponse(
    //         [
    //             'user' => $user->getUserIdentifier(),
    //             'apiToken' => $user->getApiToken(),
    //             'roles' => $user->getRoles()
    //         ]
    //     );
    // }

    //COURS:
    public function __construct(
        private EntityManagerInterface $manager,
        private SerializerInterface $serializer,
        private UserRepository $repository
    ) {
    }

    #[Route('/registration', name: 'registration', methods: 'POST')]
    /** @OA\Post(
     *     path="/api/registration",
     *     summary="Inscription d'un nouvel utilisateur",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données de l'utilisateur à inscrire",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string", example="adresse@email.com"),
     *             @OA\Property(property="password", type="string", example="Mot de passe")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Utilisateur inscrit avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="user", type="string", example="Nom d'utilisateur"),
     *             @OA\Property(property="apiToken", type="string", example="31a023e212f116124a36af14ea0c1c3806eb9378"),
     *             @OA\Property(property="roles", type="array", @OA\Items(type="string", example="ROLE_USER"))
     *         )
     *     )
     * )
     */
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $user = $this->serializer->deserialize(
            $request->getContent(),
            User::class,
            'json'
        );

        $user->setPassword(
            $passwordHasher->hashPassword($user, $user->getPassword())
        );
        $user->setCreatedAt(new \DateTimeImmutable());

        $this->manager->persist($user);
        $this->manager->flush();

        return new JsonResponse(
            [
                'user'  => $user->getUserIdentifier(),
                'apiToken' => $user->getApiToken(),
                'roles' => $user->getRoles()
            ],
            Response::HTTP_CREATED
        );
    }

    //Route login
    #[Route('/login', name: 'login', methods: 'POST')]
    public function login(#[CurrentUser] ?User $user): JsonResponse
    {
        if (null === $user) {
            return new JsonResponse(
                ['message' => 'Missing credentials'],
                Response::HTTP_UNAUTHORIZED
            );
        }
        return new JsonResponse([
            'user'  => $user->getUserIdentifier(),
            'apiToken' => $user->getApiToken(),
            'roles' => $user->getRoles(),
        ]);
    }

    //Route foncton me()
    #[Route('/me', name: 'me', methods: 'GET')]
    public function me(#[CurrentUser] ?User $user): JsonResponse
    {
        $this->repository->findOneBy(['user' => $user]);
        if ($user) {
            $this->serializer->serialize(
                $user,
                // json:
                'json'
            );
            return new JsonResponse([
                'id'  => $user->getId(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'guestNumber' => $user->getGuestNumber(),
                'allergy' => $user->getAllergy(),
                'email'  => $user->getUserIdentifier(),
                'apiToken' => $user->getApiToken(),
                'userIdentify' => $user->getEmail(),
                'username' => $user->getEmail(),
                'roles' => $user->getRoles(),
                'createdAt' => $user->getCreatedAt(),
                'updatedAt' => $user->getUpdatedAt(),
                'salt' => $user->getUpdatedAt(),
            ]);
        }

        return new JsonResponse(
            // status: 
            Response::HTTP_NOT_FOUND
        );
    }

    //Route foncton edit()
    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    public function edit(int $id, Request $request): JsonResponse
    {
        $user = $this->repository->findOneBy(['id' => $id]);
        if ($user) {
            $user =  $this->serializer->deserialize(
                $request->getContent(),
                // type: 
                User::class,
                // format: 
                'json'
            );
            return new JsonResponse([
                'password'  => $user->getPassword(),
                'updatedAt' => $user->getUpdatedAt(new \DateTimeImmutable()),

            ]);

            $this->manager->flush();
        }

        return new JsonResponse(
            // data: 
            null,
            // status: 
            Response::HTTP_NOT_FOUND
        );
    }
}
