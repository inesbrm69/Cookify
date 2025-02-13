<?php

namespace App\Controller;

use App\Repository\RecipesRepository;
use App\Entity\Recipes;
use App\Entity\QuantityFood;
use App\Entity\Food;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
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
    #[Route('/api/recipes/create', name: 'create_recipe', methods: ['POST'])]
    public function createRecipe(
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Vérification des données obligatoires de la recette
        $requiredFields = ['name', 'cookingTime', 'description', 'calories', 'quantity', 'preparationTime', 'difficulty', 'isPublic', 'ingredients', 'instructions'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return new JsonResponse(['error' => "Le champ '$field' est manquant ou vide"], Response::HTTP_BAD_REQUEST);
            }
        }

        // Création de la recette
        $recipe = new Recipes();
        $recipe->setName($data['name'])
            ->setCookingTime((int) $data['cookingTime'])
            ->setDescription($data['description'])
            ->setCalories((int) $data['calories'])
            ->setQuantity((int) $data['quantity'])
            ->setPreparationTime((int) $data['preparationTime'])
            ->setDifficulty($data['difficulty'])
            ->setIsPublic((bool) $data['isPublic']);

        // Ajout des ingrédients
        if (!is_array($data['ingredients']) || empty($data['ingredients'])) {
            return new JsonResponse(['error' => 'Les ingrédients doivent être un tableau non vide'], Response::HTTP_BAD_REQUEST);
        }

        foreach ($data['ingredients'] as $ingredientData) {
            if (!isset($ingredientData['name'], $ingredientData['quantity'], $ingredientData['unity'])) {
                return new JsonResponse(['error' => 'Données ingrédient incomplètes'], Response::HTTP_BAD_REQUEST);
            }

            // Rechercher ou créer l'aliment
            $food = $entityManager->getRepository(Food::class)->findOneBy(['name' => $ingredientData['name']]);
            if (!$food) {
                $food = new Food();
                $food->setName($ingredientData['name']);
                $entityManager->persist($food);
            }

            // Création de la relation Quantité-Ingrédient
            $quantityFood = new QuantityFood();
            $quantityFood->setQuantity((int) $ingredientData['quantity'])
                ->setUnity($ingredientData['unity'])
                ->setRecipeId($recipe)
                ->setFoodId($food);

            $entityManager->persist($quantityFood);
            $recipe->addIngredient($quantityFood);
        }

        // Ajout des instructions (conversion en texte si c'est un tableau)
        if (is_array($data['instructions'])) {
            $instructions = implode("\n", $data['instructions']);
            $recipe->setInstructions($instructions);
        } else {
            $recipe->setInstructions($data['instructions']);
        }

        // Sauvegarde de la recette dans la base de données
        $entityManager->persist($recipe);
        $entityManager->flush();

        return new JsonResponse([
            'message' => 'Recette créée avec succès',
            'recipeId' => $recipe->getId()
        ], Response::HTTP_CREATED);
    }
}