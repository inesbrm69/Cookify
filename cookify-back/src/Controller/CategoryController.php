<?php

namespace App\Controller;

use App\Repository\RecipesRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface; // Import du bon SerializerInterface

final class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/CategoryController.php',
        ]);
    }

    /**
     * Renvoie toutes les catégories
     *
     * @param CategoryRepository $repository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/categories', name: 'category.getAll', methods: ['GET'])]
    public function getAllCategories(
        CategoryRepository $repository,
        SerializerInterface $serializer
    ): JsonResponse {
        $categ = $repository->findAll();
        $jsonCateg = $serializer->serialize($categ, 'json', ["groups" => "getAllCategories"]);
        return new JsonResponse(
            $jsonCateg,
            Response::HTTP_OK,
            [],
            true
        );
    }

    /**
     * Renvoie toutes les recettes par rapport à une catégorie
     *
     * @param int $id
     * @param CategoryRepository $categoryRepository
     * @param RecipesRepository $recipesRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/recipes/categ/{id}', name: 'recipe.get_by_category', methods: ['GET'])]
    public function getRecipesByCategory(
        int $id,
        CategoryRepository $categoryRepository,
        RecipesRepository $recipesRepository,
        SerializerInterface $serializer
    ): JsonResponse {
        // Récupérer la catégorie par son ID
        $category = $categoryRepository->find($id);

        if (!$category) {
            return new JsonResponse(['error' => 'Catégorie non trouvée'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Récupérer les recettes associées à cette catégorie
        $recipes = $recipesRepository->createQueryBuilder('r')
            ->join('r.categories', 'c')
            ->where('c.id = :categoryId')
            ->setParameter('categoryId', $category->getId())
            ->getQuery()
            ->getResult();

        if (empty($recipes)) {
            return new JsonResponse(['error' => 'Aucune recette trouvée pour cette catégorie'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Sérialiser les recettes
        $jsonRecipes = $serializer->serialize($recipes, 'json', ['groups' => 'getRecipesByCategory']);

        return new JsonResponse($jsonRecipes, JsonResponse::HTTP_OK, [], true);
    }
}