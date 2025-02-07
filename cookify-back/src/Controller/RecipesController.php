<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\RecipesRepository;
use App\Entity\Recipes;


final class RecipesController extends AbstractController
{
    #[Route('/recipes', name: 'app_recipes')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/RecipesController.php',
        ]);
    }

     /**
     * Renvoie toutes les recettes
     *
     * @param RecipesRepository $repository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/recipes', name: 'recipe.getAll', methods:['GET'])]
    public function getAllRecipes(
        RecipesRepository $repository,
        SerializerInterface $serializer
        ): JsonResponse
    {
        $recipes =  $repository->findAll();
        $jsonRecipes = $serializer->serialize($recipes, 'json',["groups" => "getAllRecipes"]);
        return new JsonResponse(    
            $jsonRecipes,
            Response::HTTP_OK, 
            [], 
            true
        );
    }

    /**
     * Renvoie un nombre donné de recettes aléatoires
     *
     * @param RecipesRepository $recipesRepository
     * @param SerializerInterface $serializer
     * @param int $count
     * @return JsonResponse
     */
    #[Route('/api/recipes/random/{count}', name: 'recipe.random', methods: ['GET'])]
    public function getRandomRecipes(
        RecipesRepository $recipesRepository,
        SerializerInterface $serializer,
        int $count
    ): JsonResponse {
        
    }

    /**
     * Renvoie un nombre donné de recettes aléatoires en fonction des catégories choisies
     *
     * @param RecipesRepository $recipesRepository
     * @param CategoryRepository $categoryRepository
     * @param SerializerInterface $serializer
     * @param int $count
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/recipes/random/{count}', name: 'recipe.random.filtered', methods: ['GET'])]
    public function getRandomRecipesByCategories(
        RecipesRepository $recipesRepository,
        CategoryRepository $categoryRepository,
        SerializerInterface $serializer,
        int $count,
        Request $request
    ): JsonResponse {
        // Récupérer les IDs des catégories depuis les paramètres GET
        $categoryIds = $request->query->get('categories'); // Exemple: ?categories=1,2,3

        if (!$categoryIds) {
            // Récupérer toutes les recettes
            $allRecipes = $recipesRepository->findAll();

            if(!$allRecipes) {
                return new JsonResponse([
                    'error' => 'Aucune recette trouvée'
                ], 404);
            }
            if (count($allRecipes) < $count) {
                return new JsonResponse([
                    'error' => 'Nombre de recettes demandé trop grand'
                ], 400);
            }
            // Mélanger aléatoirement et sélectionner `$count` recettes
            shuffle($allRecipes);
            $randomRecipes = array_slice($allRecipes, 0, $count);
            // Sérialiser en JSON
            $jsonRecipes = $serializer->serialize($randomRecipes, 'json', ['groups' => 'getRecipes']);
        }else{
            // Convertir les IDs en tableau
            $categoryIdsArray = explode(',', $categoryIds);

            // Récupérer les recettes appartenant aux catégories sélectionnées
            $recipes = $recipesRepository->findByCategories($categoryIdsArray);

            // Vérifier si on a assez de recettes
            if (count($recipes) < $count) {
                return new JsonResponse([
                    'error' => 'Nombre de recettes demandé trop grand pour ces catégories'
                ], 400);
            }

            // Mélanger aléatoirement et sélectionner `$count` recettes
            shuffle($recipes);
            $randomRecipes = array_slice($recipes, 0, $count);

            // Sérialiser en JSON
            $jsonRecipes = $serializer->serialize($randomRecipes, 'json', ['groups' => 'getRecipes']);
        }

        return new JsonResponse($jsonRecipes, 200, [], true);
    }

    /**
     * Remplace une recette spécifique par une autre sélectionnée
     *
     * @param RecipesRepository $recipesRepository
     * @param int $oldRecipeId
     * @param int $newRecipeId
     * @return JsonResponse
     */
    #[Route('/api/recipes/replace/{oldRecipeId}/{newRecipeId}', name: 'recipe.replace.selected', methods: ['PUT'])]
    public function replaceRecipeWithSelected(
        RecipesRepository $recipesRepository,
        int $oldRecipeId,
        int $newRecipeId
    ): JsonResponse {
        // Vérifier si les deux recettes existent
        $oldRecipe = $recipesRepository->find($oldRecipeId);
        $newRecipe = $recipesRepository->find($newRecipeId);

        if (!$oldRecipe || !$newRecipe) {
            return new JsonResponse(['error' => 'Une des recettes n\'existe pas'], 404);
        }

        return new JsonResponse([
            'message' => 'Recette remplacée avec succès',
            'oldRecipe' => ['id' => $oldRecipe->getId(), 'name' => $oldRecipe->getName()],
            'newRecipe' => ['id' => $newRecipe->getId(), 'name' => $newRecipe->getName()]
        ], 200);
    }

    /**
     * Permet à un utilisateur authentifié de créer une recette
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param CategoryRepository $categoryRepository
     * @return JsonResponse
     */
    #[Route('/api/recipes/create', name: 'recipe.create', methods: ['POST'])]
    public function createRecipe(
        Request $request,
        Recipes $recipe,
        EntityManagerInterface $entityManager,
        Security $security,
        CategoryRepository $categoryRepository
    ): JsonResponse {
        // Vérifier que l'utilisateur est connecté
        $user = $security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Vous devez être connecté pour créer une recette'], 401);
        }

        // Récupérer les données JSON envoyées
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return new JsonResponse(['error' => 'Données invalides'], 400);
        }

        // Vérifier que toutes les informations essentielles sont présentes
        if (!isset($data['name'], $data['cookingTime'], $data['description'], $data['calories'], 
            $data['quantity'], $data['preparationTime'], $data['instructions'], 
            $data['difficulty'], $data['isPublic'], $data['categories'])) {
            return new JsonResponse(['error' => 'Données incomplètes'], 400);
        }

        // Créer une nouvelle recette
        $recipe->setName($data['name'])
        ->setCookingTime($data['cookingTime'])
        ->setDescription($data['description'])
        ->setCalories($data['calories'])
        ->setQuantity($data['quantity'])
        ->setPreparationTime($data['preparationTime'])
        ->setInstructions($data['instructions'])
        ->setDifficulty($data['difficulty'])
        ->setIsPublic($data['isPublic'])
        ->setCreatedBy($user); // Associer la recette à l'utilisateur connecté

        // Ajouter les catégories
        foreach ($data['categories'] as $categoryId) {
            $category = $categoryRepository->find($categoryId);
            if ($category) {
                $recipe->addCategory($category);
            }
        }

        // Sauvegarde en base de données
        $entityManager->persist($recipe);
        $entityManager->flush();

        return new JsonResponse([
            'message' => 'Recette créée avec succès',
            'recipe' => [
                'id' => $recipe->getId(),
                'name' => $recipe->getName(),
                'createdBy' => $user->getUserIdentifier(),
            ]
        ], 201);
    }

    /**
     * Supprime une recette de la liste sélectionnée (sans supprimer la recette en base)
     *
     * @param int $recipeId
     * @param SessionInterface $session
     * @return JsonResponse
     */
    #[Route('/api/recipes/remove/{recipeId}', name: 'recipe.remove', methods: ['DELETE'])]
    public function removeRecipeFromList(int $recipeId, SessionInterface $session): JsonResponse
    {
        // Récupérer la liste actuelle des recettes depuis la session
        $selectedRecipes = $session->get('selected_recipes', []);

        // Vérifier si la recette est dans la liste
        if (!in_array($recipeId, $selectedRecipes)) {
            return new JsonResponse([
                'error' => 'La recette n\'est pas dans la liste'
            ], 404);
        }

        // Supprimer la recette de la liste
        $selectedRecipes = array_filter($selectedRecipes, fn($id) => $id != $recipeId);

        // Sauvegarder la liste mise à jour en session
        $session->set('selected_recipes', array_values($selectedRecipes));

        return new JsonResponse([
            'message' => 'Recette retirée de la liste avec succès',
            'updatedList' => $selectedRecipes
        ], 200);
    }
}

