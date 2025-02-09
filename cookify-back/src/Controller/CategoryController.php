<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

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
    #[Route('/api/categories', name: 'category.getAll', methods:['GET'])]
    public function getAllCategories(
        CategoryRepository $repository,
        SerializerInterface $serializer
        ): JsonResponse
    {
        $categ =  $repository->findAll();
        $jsonCateg = $serializer->serialize($categ, 'json',["groups" => "getAllCategories"]);
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
     * @param Category $categ
     * @param RecipesRepository $recipesRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/recipes/categ/{id}', name: 'recipe.get', methods:['GET'])]
    public function getRecipesByCategorie(
        Category $categ,
        RecipesRepository $recipesRepository,
        SerializerInterface $serializer
    ): JsonResponse
    {
        $recipes = $recipesRepository->findBy(['category' => $categ]);
        $jsonRecipes = $serializer->serialize($recipes, 'json', ['groups' => "getRecipesByCategorie"]);

        return new JsonResponse($jsonRecipes, 200, [], true);
    }

}
