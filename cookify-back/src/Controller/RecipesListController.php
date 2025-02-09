<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;

final class RecipesListController extends AbstractController
{
    #[Route('/recipes/list', name: 'app_recipes_list')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/RecipesListController.php',
        ]);
    }

    #[Route('/api/recipelist/{listId}', name: 'recipelist.get', methods: ['GET'])]
    public function getRecipeList(int $listId, RecipeListRepository $recipeListRepository): JsonResponse {
        $recipeList = $recipeListRepository->find($listId);
        if (!$recipeList) {
            return new JsonResponse(['error' => 'Liste non trouvée'], 404);
        }
        
        return new JsonResponse([
            'listId' => $recipeList->getId(),
            'name' => $recipeList->getName(),
            'recipes' => array_map(fn($r) => ['id' => $r->getId(), 'name' => $r->getName()], $recipeList->getRecipes()->toArray())
        ], 200);
    }


    /**
     * Génère une liste aléatoire de recettes en fonction des préférences de l'utilisateur
     *
     * @param int $count
     * @param Request $request
     * @param RecipesList $recipesList
     * @param RecipesRepository $recipesRepository
     * @param CategoryRepository $categoryRepository
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @return JsonResponse
     */
    #[Route('/api/recipelist/generate/{count}', name: 'recipelist.generate', methods: ['POST'])]
    public function generateRecipeList(
        int $count,
        Request $request,
        RecipesList $recipesList,
        RecipesRepository $recipesRepository,
        CategoryRepository $categoryRepository,
        EntityManagerInterface $entityManager,
        Security $security
    ): JsonResponse {
        // Vérifier que l'utilisateur est connecté
        $user = $security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Vous devez être connecté'], 401);
        }

        // Récupérer les préférences depuis le JSON envoyé
        $data = json_decode($request->getContent(), true);
        $categoryIds = $data['categories'] ?? []; // Ex: [1, 3, 5]
        
        // Si aucune catégorie spécifiée, récupérer toutes les recettes
        if (empty($categoryIds)) {
            $recipes = $recipesRepository->findAll();
        } else {
            $recipes = $recipesRepository->findByCategories($categoryIds);
        }

        // Vérifier s'il y a assez de recettes
        if (count($recipes) < $count) {
            return new JsonResponse([
                'error' => 'Pas assez de recettes disponibles selon vos critères'
            ], 400);
        }

        // Mélanger et sélectionner $count recettes aléatoirement
        shuffle($recipes);
        $selectedRecipes = array_slice($recipes, 0, $count);

        // Créer une nouvelle liste de recettes en base
        $recipesList->setOwner($user);

        // Ajouter les recettes sélectionnées à la liste
        foreach ($selectedRecipes as $recipe) {
            $recipeList->addRecipe($recipe);
        }

        // Sauvegarder la liste en base
        $entityManager->persist($recipeList);
        $entityManager->flush();

        // Retourner la liste en JSON
        return new JsonResponse([
            'message' => 'Liste générée avec succès',
            'listId' => $recipeList->getId(),
            'recipes' => array_map(fn($r) => ['id' => $r->getId()], $selectedRecipes)
        ], 201);
    }

    /**
     * Remplace une recette dans une liste par une autre sélectionnée
     *
     * @param int $listId
     * @param int $oldRecipeId
     * @param int $newRecipeId
     * @return JsonResponse
     */
    #[Route('/api/recipelist/{listId}/replace/{oldRecipeId}/{newRecipeId}', name: 'recipelist.replaceRecipe', methods: ['PUT'])]
    public function replaceRecipeInList(
        int $listId,
        int $oldRecipeId,
        int $newRecipeId,
        RecipeListRepository $recipeListRepository,
        RecipesRepository $recipesRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        // Trouver la liste
        $recipeList = $recipeListRepository->find($listId);
        if (!$recipeList) {
            return new JsonResponse(['error' => 'Liste non trouvée'], 404);
        }

        // Trouver les recettes
        $oldRecipe = $recipesRepository->find($oldRecipeId);
        $newRecipe = $recipesRepository->find($newRecipeId);

        if (!$oldRecipe || !$newRecipe) {
            return new JsonResponse(['error' => 'Une des recettes n\'existe pas'], 404);
        }

        // Vérifier si la recette à remplacer est bien dans la liste
        if (!$recipeList->getRecipes()->contains($oldRecipe)) {
            return new JsonResponse(['error' => 'La recette à remplacer n\'est pas dans la liste'], 400);
        }

        // Remplacer l'ancienne recette par la nouvelle
        $recipeList->removeRecipe($oldRecipe);
        $recipeList->addRecipe($newRecipe);

        // Sauvegarde en base de données
        $entityManager->flush();

        return new JsonResponse([
            'message' => 'Recette remplacée avec succès dans la liste',
            'oldRecipe' => ['id' => $oldRecipe->getId(), 'name' => $oldRecipe->getName()],
            'newRecipe' => ['id' => $newRecipe->getId(), 'name' => $newRecipe->getName()]
        ], 200);
    }

    /**
     * Supprime une recette d'une liste sans la supprimer en base
     *
     * @param int $listId
     * @param int $recipeId
     * @param RecipeListRepository $recipeListRepository
     * @param RecipesRepository $recipesRepository
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[Route('/api/recipelist/{listId}/remove/{recipeId}', name: 'recipelist.removeRecipe', methods: ['DELETE'])]
    public function removeRecipeFromList(
        int $listId,
        int $recipeId,
        RecipeListRepository $recipeListRepository,
        RecipesRepository $recipesRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        // Récupérer la liste de recettes
        $recipeList = $recipeListRepository->find($listId);
        if (!$recipeList) {
            return new JsonResponse(['error' => 'Liste non trouvée'], 404);
        }

        // Récupérer la recette
        $recipe = $recipesRepository->find($recipeId);
        if (!$recipe) {
            return new JsonResponse(['error' => 'Recette non trouvée'], 404);
        }

        if (!$recipeList->getRecipes()->contains($recipe)) {
            return new JsonResponse(['error' => 'La recette n\'est pas dans cette liste'], 400);
        }

        $recipeList->removeRecipe($recipe);
        $entityManager->flush();

        return new JsonResponse([
            'message' => 'Recette retirée de la liste avec succès',
            'updatedList' => array_map(fn($r) => ['id' => $r->getId()], $recipeList->getRecipes()->toArray())
        ], 200);
    }

}
