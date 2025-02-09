<?php

namespace App\Controller;

use App\Repository\RecipesRepository;
use App\Entity\Recipes;
use App\Entity\QuantityFood;
use App\Entity\Food;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
     */
    #[Route('/api/recipes', name: 'recipe.getAll', methods: ['GET'])]
    public function getAllRecipes(
        RecipesRepository $repository,
        SerializerInterface $serializer
    ): JsonResponse {
        $recipes = $repository->findAll();
        $jsonRecipes = $serializer->serialize($recipes, 'json', ["groups" => "getAllRecipes"]);
        return new JsonResponse($jsonRecipes, Response::HTTP_OK, [], true);
    }

    /**
     * Crée une recette avec ses ingrédients et instructions
     */
    #[Route('/api/recipes/create', name: 'recipes.create', methods: ['POST'])]
    public function createRecipe(
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Vérification des données
        if (!isset($data['name'], $data['cookingTime'], $data['description'], 
                   $data['calories'], $data['quantity'], $data['preparationTime'], 
                   $data['difficulty'], $data['isPublic'], $data['ingredients'], $data['instructions'])) {
            return new JsonResponse(['error' => 'Données incomplètes'], Response::HTTP_BAD_REQUEST);
        }

        // Création de la recette
        $recipe = new Recipes();
        $recipe->setName($data['name'])
            ->setCookingTime($data['cookingTime'])
            ->setDescription($data['description'])
            ->setCalories($data['calories'])
            ->setQuantity($data['quantity'])
            ->setPreparationTime($data['preparationTime'])
            ->setDifficulty($data['difficulty'])
            ->setIsPublic($data['isPublic']);

        // Ajout des ingrédients
        foreach ($data['ingredients'] as $ingredientData) {
            if (!isset($ingredientData['foodId'], $ingredientData['quantity'], $ingredientData['unity'])) {
                return new JsonResponse(['error' => 'Données ingrédient incomplètes'], Response::HTTP_BAD_REQUEST);
            }

            $food = $entityManager->getRepository(Food::class)->find($ingredientData['foodId']);
            if (!$food) {
                return new JsonResponse(['error' => 'Ingrédient non trouvé'], Response::HTTP_NOT_FOUND);
            }

            $quantityFood = new QuantityFood();
            $quantityFood->setQuantity($ingredientData['quantity'])
                ->setUnity($ingredientData['unity'])
                ->setRecipeId($recipe)
                ->setFoodId($food);

            $entityManager->persist($quantityFood);
            $recipe->addIngredient($quantityFood);
        }

        // Ajout des instructions (avec vérification)
        if (!empty($data['instructions']) && is_array($data['instructions'])) {
            $instructions = implode("\n", $data['instructions']);
            $recipe->setInstructions($instructions);
        } else {
            return new JsonResponse(['error' => 'Instructions invalides'], Response::HTTP_BAD_REQUEST);
        }

        // Sauvegarde en base
        $entityManager->persist($recipe);
        $entityManager->flush();

        return new JsonResponse([
            'message' => 'Recette créée avec succès',
            'recipeId' => $recipe->getId()
        ], Response::HTTP_CREATED);
    }
}