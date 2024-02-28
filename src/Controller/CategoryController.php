<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
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
#[Route('api/category', name: 'app_api_category_')]
class CategoryController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private CategoryRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    // //creation des fonctions CRUD
    // #[Route(name: 'new', methods: 'POST')]
    #[Route(methods: 'POST')]
    // //fonction Create (crée)
    // public function new(): Response
    public function new(Request $request): JsonResponse
    {
        //     $category = new category();
        //     $category->setTitle(title: 'Plats');
        //     $category->setCreatedAt(new \DateTimeImmutable());

        //     //A stocker en BDD
        //     // Tell Doctrine you want to (eventually) save the restaurant (no queries yet)
        //     // Dites à Doctrine que vous souhaitez (éventuellement) sauver le restaurant (aucune requête pour l'instant)
        //     $this->manager->persist($category);

        //     // Actually executes the queries (i.e. the INSERT query)
        //     // Exécute réellement les requêtes (c'est-à-dire la requête INSERT)
        //     $this->manager->flush();

        //     return $this->json(
        //         ['message' => "Category resource created with {$category->getId()} id"],
        //         Response::HTTP_CREATED,
        //     );

        $category = $this->serializer->deserialize(
            $request->getContent(),
            Category::class,
            'json'
        );
        $category->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($category);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($category, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_category_show',
            ['id' => $category->getId()],
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
        //     //Chercher restaurant {id} = 1
        //     $category = $this->repository->findOneBy(['id' => $id]);

        //     if (!$category) {
        //         throw $this->createNotFoundException("No Category found for {$id} id");
        //         // throw new \Exception(message: "No Category found for {$id} id");
        //     }

        //     return $this->json(
        //         ['message' => "A Category was found : {$category->getTitle()} for {$category->getId()} id"]
        //     );

        $category = $this->repository->findOneBy(['id' => $id]);
        if ($category) {
            $responseData = $this->serializer->serialize(
                $category,
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
    // //fonction Update (réecrire)
    // public function edit(int $id): Response
    public function edit(int $id, Request $request): JsonResponse
    {
        //     $category = $this->repository->findOneBy(['id' => $id]);
        //     if (!$category) {
        //         // throw $this->createNotFoundException("No Category found for {$id} id");
        //         throw new \Exception(message: "No Category found for {$id} id");
        //     }

        //     $category->setTitle('Category name updated');

        //     $this->manager->flush();

        //     return $this->redirectToRoute(
        //         'app_api_category_show',
        //         ['id' => $category->getId()]
        //     );

        $category = $this->repository->findOneBy(['id' => $id]);
        if ($category) {
            $category = $this->serializer->deserialize(
                $request->getContent(),
                Category::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $category]
            );
            $category->setUpdatedAt(new DateTimeImmutable());

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
        //     $category = $this->repository->findOneBy(['id' => $id]);
        //     if (!$category) {
        //         // throw $this->createNotFoundException("No Category found for {$id} id");
        //         throw new \Exception(message: "No Category found for {$id} id");
        //     }

        //     $this->manager->remove($category);

        //     $this->manager->flush();

        //     return $this->json(
        //         ['message' => "Category resource deleted"],
        //         Response::HTTP_NO_CONTENT,
        //     );

        $category = $this->repository->findOneBy(['id' => $id]);
        if ($category) {
            $this->manager->remove($category);
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
