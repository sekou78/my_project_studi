<?php

namespace App\Controller;

use App\Entity\Food;
use App\Repository\FoodRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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

    public function __construct(private EntityManagerInterface $manager, private FoodRepository $repository)
    {
    }

    //creation des fonctions CRUD
    #[Route(name: 'new', methods: 'POST')]
    //fonction Create (crée)
    public function new(): Response
    {
        $food = new Food();
        $food->setTitle(title: 'Fakoye');
        $food->setDescription(description: 'Plat Malien du peuple Peulh');
        $food->setPrice(price: 5.80);
        $food->setCreatedAt(new \DateTimeImmutable());

        //A stocker en BDD
        // Tell Doctrine you want to (eventually) save the food (no queries yet)
        // Dites à Doctrine que vous souhaitez (éventuellement) sauver le plat (aucune requête pour l'instant)
        $this->manager->persist($food);

        // Actually executes the queries (i.e. the INSERT query)
        // Exécute réellement les requêtes (c'est-à-dire la requête INSERT)
        $this->manager->flush();

        return $this->json(
            ['message' => "Food resource created with {$food->getId()} id"],
            Response::HTTP_CREATED,
        );
    }

    #[Route('/{id}', name: 'show', methods: 'GET')]
    public function show(int $id): Response  //fonction Read (lire)
    {
        //Chercher food {id} = 1
        $food = $this->repository->findOneBy(['id' => $id]);

        if (!$food) {
            // throw $this->createNotFoundException("No Food found for {$id} id");
            throw new \Exception(message: "No food found for {$id} id");
        }

        return $this->json(
            ['message' => "A Food was found : {$food->getTitle()} for {$food->getId()} id"]
        );
    }

    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    //fonction Update (réecrire)
    public function edit(int $id): Response
    {
        $food = $this->repository->findOneBy(['id' => $id]);
        if (!$food) {
            // throw $this->createNotFoundException("No Food found for {$id} id");
            throw new \Exception(message: "No Food found for {$id} id");
        }

        $food->setTitle('Food name updated');

        $this->manager->flush();

        return $this->redirectToRoute(
            'app_api_food_show',
            ['id' => $food->getId()]
        );
    }

    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    public function delete(int $id): Response  //fonction Delete (supprimer)
    {
        $food = $this->repository->findOneBy(['id' => $id]);
        if (!$food) {
            // throw $this->createNotFoundException("No Food found for {$id} id");
            throw new \Exception(message: "No Food found for {$id} id");
        }

        $this->manager->remove($food);

        $this->manager->flush();

        return $this->json(
            ['message' => "Food resource deleted"],
            Response::HTTP_NO_CONTENT
        );
    }
}
