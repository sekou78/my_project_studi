<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

//creation d'une route globales
#[Route('api/category', name: 'app_api_category_')]
class CategoryController extends AbstractController
{
    // #[Route('/category', name: 'app_category')]
    // public function index(): Response
    // {
    //     return $this->render('category/index.html.twig', [
    //         'controller_name' => 'CategoryController',
    //     ]);
    // }


    public function __construct(private EntityManagerInterface $manager, private CategoryRepository $repository)
    {
    }

    //creation des fonctions CRUD
    #[Route(name: 'new', methods: 'POST')]
    //fonction Create (crée)
    public function new(): Response
    {
        $category = new Category();
        $category->setTitle(title: 'Fakoye');
        $category->setCreatedAt(new \DateTimeImmutable());

        //A stocker en BDD
        // Tell Doctrine you want to (eventually) save the food (no queries yet)
        // Dites à Doctrine que vous souhaitez (éventuellement) sauver le plat (aucune requête pour l'instant)
        $this->manager->persist($category);

        // Actually executes the queries (i.e. the INSERT query)
        // Exécute réellement les requêtes (c'est-à-dire la requête INSERT)
        $this->manager->flush();

        return $this->json(
            ['message' => "Category resource created with {$category->getId()} id"],
            Response::HTTP_CREATED,
        );
    }

    #[Route('/{id}', name: 'show', methods: 'GET')]
    public function show(int $id): Response  //fonction Read (lire)
    {
        //Chercher food {id} = 1
        $category = $this->repository->findOneBy(['id' => $id]);

        if (!$category) {
            throw $this->createNotFoundException("No Category found for {$id} id");
            // throw new \Exception(message: "No food found for {$id} id");
        }

        return $this->json(
            ['message' => "A Category was found : {$category->getTitle()} for {$category->getId()} id"]
        );
    }

    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    //fonction Update (réecrire)
    public function edit(int $id): Response
    {
        $category = $this->repository->findOneBy(['id' => $id]);
        if (!$category) {
            throw $this->createNotFoundException("No Category found for {$id} id");
            // throw new \Exception(message: "No Food found for {$id} id");
        }

        $category->setTitle('Category name updated');

        $this->manager->flush();

        return $this->redirectToRoute(
            'app_api_category_show',
            ['id' => $category->getId()]
        );
    }

    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    public function delete(int $id): Response  //fonction Delete (supprimer)
    {
        $category = $this->repository->findOneBy(['id' => $id]);
        if (!$category) {
            throw $this->createNotFoundException("No Category found for {$id} id");
            // throw new \Exception(message: "No Food found for {$id} id");
        }

        $this->manager->remove($category);

        $this->manager->flush();

        return $this->json(
            ['message' => "Category resource deleted"],
            Response::HTTP_NO_CONTENT
        );
    }
}
