<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Repository\RestaurantRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

//creation d'une route globales
#[Route('api/restaurant', name: 'app_api_restaurant_')]
class RestaurantController extends AbstractController
{
    // #[Route('/restaurant', name: 'app_restaurant')]
    // public function index(): Response
    // {
    //     return $this->render('restaurant/index.html.twig', [
    //         'controller_name' => 'RestaurantController',
    //     ]);
    // }

    public function __construct(
        private EntityManagerInterface $manager,
        private RestaurantRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    //creation des fonctions CRUD
    // #[Route(name: 'new', methods: 'POST')]
    #[Route(methods: 'POST')]
    //fonction Create (crée)
    // public function new(): Response
    public function new(Request $request): JsonResponse
    {
        // $restaurant = new Restaurant();
        // $restaurant->setName(name: 'Quai Antique');
        // $restaurant->setDescription(description: 'Cette qualité et ce goût par le chef Arnaud MICHANT.');
        // $restaurant->setCreatedAt(new \DateTimeImmutable());

        // //A stocker en BDD
        // // Tell Doctrine you want to (eventually) save the restaurant (no queries yet)
        // // Dites à Doctrine que vous souhaitez (éventuellement) sauver le restaurant (aucune requête pour l'instant)
        // $this->manager->persist($restaurant);

        // // Actually executes the queries (i.e. the INSERT query)
        // // Exécute réellement les requêtes (c'est-à-dire la requête INSERT)
        // $this->manager->flush();

        // return $this->json(
        //     ['message' => "Restaurant resource created with {$restaurant->getId()} id"],
        //     Response::HTTP_CREATED,
        // );

        $restaurant = $this->serializer->deserialize(
            $request->getContent(),
            // type: 
            Restaurant::class,
            // format: 
            'json'
        );
        $restaurant->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($restaurant);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($restaurant, 'json');
        $location = $this->urlGenerator->generate(
            // name: 
            'app_api_restaurant_show',
            ['id' => $restaurant->getId()],
            // referenceType: 
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse(
            // data: 
            $responseData,
            // status: 
            Response::HTTP_CREATED,
            ["Location" => $location],
            // json:
            true
        );

        // return new JsonResponse(
        //     null,
        //     status: Response::HTTP_CREATED,
        //     // [],
        //     json: true
        // );
    }

    #[Route('/{id}', name: 'show', methods: 'GET')]
    // // public function show(int $id): Response  //fonction Read (lire)
    public function show(int $id): JsonResponse
    {
        // //Chercher restaurant {id} = 1
        // $restaurant = $this->repository->findOneBy(['id' => $id]);

        // if (!$restaurant) {
        //     throw $this->createNotFoundException("No Restaurant found for {$id} id");
        //     // throw new \Exception(message: "No Restaurant found for {$id} id");
        // }

        // return $this->json(
        //     ['message' => "A Restaurant was found : {$restaurant->getName()} for {$restaurant->getId()} id"]
        // );

        $restaurant = $this->repository->findOneBy(['id' => $id]);
        if ($restaurant) {
            $responseData = $this->serializer->serialize(
                $restaurant,
                // json:
                'json'
            );

            return new JsonResponse(
                $responseData,
                // status:
                Response::HTTP_OK,
                [],
                // json:
                true
            );
        }

        return new JsonResponse(
            // data: 
            null,
            // status: 
            Response::HTTP_NOT_FOUND
        );
    }

    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    //fonction Update (réecrire)
    // public function edit(int $id): Response
    public function edit(int $id, Request $request): JsonResponse
    {
        // $restaurant = $this->repository->findOneBy(['id' => $id]);
        // if (!$restaurant) {
        //     // throw $this->createNotFoundException("No Restaurant found for {$id} id");
        //     throw new \Exception(message: "No Restaurant found for {$id} id");
        // }

        // $restaurant->setName('Restaurant name updated');

        // $this->manager->flush();

        // return $this->redirectToRoute(
        //     'app_api_restaurant_show',
        //     ['id' => $restaurant->getId()]
        // );

        $restaurant = $this->repository->findOneBy(['id' => $id]);
        if ($restaurant) {
            $restaurant = $this->serializer->deserialize(
                $request->getContent(),
                // type: 
                Restaurant::class,
                // format: 
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $restaurant]
            );
            $restaurant->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            return new JsonResponse(
                // data:
                null,
                // status: 
                Response::HTTP_NO_CONTENT
            );
        }

        return new JsonResponse(
            // data: 
            null,
            // status: 
            Response::HTTP_NOT_FOUND
        );
    }

    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    // public function delete(int $id): Response  //fonction Delete (supprimer)
    public function delete(int $id): JsonResponse
    {
        // $restaurant = $this->repository->findOneBy(['id' => $id]);
        // if (!$restaurant) {
        //     // throw $this->createNotFoundException("No Restaurant found for {$id} id");
        //     throw new \Exception(message: "No Restaurant found for {$id} id");
        // }

        // $this->manager->remove($restaurant);

        // $this->manager->flush();

        // return $this->json(
        //     ['message' => "Restaurant resource deleted"],
        //     Response::HTTP_NO_CONTENT
        // );

        $restaurant = $this->repository->findOneBy(['id' => $id]);
        if ($restaurant) {
            $this->manager->remove($restaurant);
            $this->manager->flush();

            return new JsonResponse(
                // data: 
                null,
                // status: 
                Response::HTTP_NO_CONTENT
            );
        }

        return new JsonResponse(
            // data: 
            null,
            // status: 
            Response::HTTP_NOT_FOUND
        );
    }
}
