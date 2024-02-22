<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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

    public function __construct(private EntityManagerInterface $manager, private RestaurantRepository $repository)
    {
    }

    //creation des fonctions CRUD
    #[Route(name: 'new', methods: 'POST')]
    //fonction Create (crée)
    public function new(): Response
    {
        $restaurant = new Restaurant();
        $restaurant->setName(name: 'Quai Antique');
        $restaurant->setDescription(description: 'Cette qualité et ce goût par le chef Arnaud MICHANT.');
        $restaurant->setCreatedAt(new \DateTimeImmutable());

        //A stocker en BDD
        // Tell Doctrine you want to (eventually) save the restaurant (no queries yet)
        // Dites à Doctrine que vous souhaitez (éventuellement) sauver le restaurant (aucune requête pour l'instant)
        $this->manager->persist($restaurant);

        // Actually executes the queries (i.e. the INSERT query)
        // Exécute réellement les requêtes (c'est-à-dire la requête INSERT)
        $this->manager->flush();

        return $this->json(
            ['message' => "Restaurant resource created with {$restaurant->getId()} id"],
            Response::HTTP_CREATED,
        );
    }

    #[Route('/{id}', name: 'show', methods: 'GET')]
    public function show(int $id): Response  //fonction Read (lire)
    {
        //Chercher restaurant {id} = 1
        $restaurant = $this->repository->findOneBy(['id' => $id]);

        if (!$restaurant) {
            throw $this->createNotFoundException("No Restaurant found for {$id} id");
            // throw new \Exception(message: "No Restaurant found for {$id} id");
        }

        return $this->json(
            ['message' => "A Restaurant was found : {$restaurant->getName()} for {$restaurant->getId()} id"]
        );
    }

    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    //fonction Update (réecrire)
    public function edit(int $id): Response
    {
        $restaurant = $this->repository->findOneBy(['id' => $id]);
        if (!$restaurant) {
            // throw $this->createNotFoundException("No Restaurant found for {$id} id");
            throw new \Exception(message: "No Restaurant found for {$id} id");
        }

        $restaurant->setName('Restaurant name updated');

        $this->manager->flush();

        return $this->redirectToRoute(
            'app_api_restaurant_show',
            ['id' => $restaurant->getId()]
        );
    }

    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    public function delete(int $id): Response  //fonction Delete (supprimer)
    {
        $restaurant = $this->repository->findOneBy(['id' => $id]);
        if (!$restaurant) {
            // throw $this->createNotFoundException("No Restaurant found for {$id} id");
            throw new \Exception(message: "No Restaurant found for {$id} id");
        }

        $this->manager->remove($restaurant);

        $this->manager->flush();

        return $this->json(
            ['message' => "Restaurant resource deleted"],
            Response::HTTP_NO_CONTENT
        );
    }
}
