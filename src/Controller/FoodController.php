<?php

namespace App\Controller;

use App\Entity\Food;
use App\Repository\FoodRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

//creation d'une route globales
#[Route('api/food', name: 'app_api_food_')]
class FoodController extends AbstractController
{
    // #[Route('/food', name: 'app_food')]
    // public function index(): Response
    // {
    //     return $this->render('food/index.html.twig', [
    //         'controller_name' => 'FoodController',
    //     ]);
    // }

    public function __construct(
        private EntityManagerInterface $manager,
        private FoodRepository $repository,
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
        // $food = new Food();
        // $food->setTitle(title: 'Fakoye');
        // $food->setDescription(description: 'Plat Malien du peuple Peulh');
        // $food->setPrice(price: 5.80);
        // $food->setCreatedAt(new \DateTimeImmutable());

        // //A stocker en BDD
        // // Tell Doctrine you want to (eventually) save the food (no queries yet)
        // // Dites à Doctrine que vous souhaitez (éventuellement) sauver le plat (aucune requête pour l'instant)
        // $this->manager->persist($food);

        // // Actually executes the queries (i.e. the INSERT query)
        // // Exécute réellement les requêtes (c'est-à-dire la requête INSERT)
        // $this->manager->flush();

        // return $this->json(
        //     ['message' => "Food resource created with {$food->getId()} id"],
        //     Response::HTTP_CREATED,
        // );

        $food = $this->serializer->deserialize(
            $request->getContent(),
            Food::class,
            'json'
        );
        $food->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($food);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($food, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_food_show',
            ['id' => $food->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse(
            $responseData,
            Response::HTTP_CREATED,
            ["Location" => $location],
            true
        );
    }

    #[Route('/{id}', name: 'show', methods: 'GET')]
    // public function show(int $id): Response  //fonction Read (lire)
    public function show(int $id): JsonResponse
    {
        // //Chercher food {id} = 1
        // $food = $this->repository->findOneBy(['id' => $id]);

        // if (!$food) {
        //     // throw $this->createNotFoundException("No Food found for {$id} id");
        //     throw new \Exception(message: "No food found for {$id} id");
        // }

        // return $this->json(
        //     ['message' => "A Food was found : {$food->getTitle()} for {$food->getId()} id"]
        // );

        $food = $this->repository->findOneBy(['id' => $id]);
        if ($food) {
            $responseData = $this->serializer->serialize(
                $food,
                'json'
            );

            return new JsonResponse(
                $responseData,
                Response::HTTP_OK,
                [],
                true
            );
        }
        return new JsonResponse(
            null,
            Response::HTTP_NOT_FOUND
        );
    }

    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    //fonction Update (réecrire)
    // public function edit(int $id): Response
    public function edit(int $id, Request $request): JsonResponse
    {
        // $food = $this->repository->findOneBy(['id' => $id]);
        // if (!$food) {
        //     // throw $this->createNotFoundException("No Food found for {$id} id");
        //     throw new \Exception(message: "No Food found for {$id} id");
        // }

        // $food->setTitle('Food name updated');

        // $this->manager->flush();

        // return $this->redirectToRoute(
        //     'app_api_food_show',
        //     ['id' => $food->getId()]
        // );

        $food = $this->repository->findOneBy(['id' => $id]);
        if ($food) {
            $food = $this->serializer->deserialize(
                $request->getContent(),
                Food::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $food]
            );
            $food->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            return new JsonResponse(
                null,
                Response::HTTP_NO_CONTENT
            );
        }

        return new JsonResponse(
            null,
            Response::HTTP_NOT_FOUND
        );
    }

    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    // public function delete(int $id): Response  //fonction Delete (supprimer)
    public function delete(int $id): JsonResponse
    {
        // $food = $this->repository->findOneBy(['id' => $id]);
        // if (!$food) {
        //     // throw $this->createNotFoundException("No Food found for {$id} id");
        //     throw new \Exception(message: "No Food found for {$id} id");
        // }

        // $this->manager->remove($food);

        // $this->manager->flush();

        // return $this->json(
        //     ['message' => "Food resource deleted"],
        //     Response::HTTP_NO_CONTENT
        // );

        $food = $this->repository->findOneBy(['id' => $id]);
        if ($food) {
            $this->manager->remove($food);
            $this->manager->flush();

            return new JsonResponse(
                null,
                Response::HTTP_NO_CONTENT
            );
        }

        return new JsonResponse(
            null,
            Response::HTTP_NOT_FOUND
        );
    }
}
