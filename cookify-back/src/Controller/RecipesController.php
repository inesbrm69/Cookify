<?php

namespace App\Controller;

use App\Repository\RecipesRepository;
use App\Entity\Recipes;
use App\Entity\QuantityFood;
use App\Entity\Food;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Category;

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
        // Vérifier si l'utilisateur est authentifié
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $recipes = $repository->findAll();
        $jsonRecipes = $serializer->serialize($recipes, 'json', ["groups" => "getAllRecipes"]);
        return new JsonResponse($jsonRecipes, Response::HTTP_OK, [], true);
    }

    /**
     * Renvoie une recette par son ID
     */
    #[Route('/api/recipes/{id}', name: 'recipe.getById', methods: ['GET'])]
    public function getRecipeById(
        int $id,
        RecipesRepository $repository,
        SerializerInterface $serializer
    ): JsonResponse {
        // Vérifier si l'utilisateur est authentifié
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Récupérer la recette par ID
        $recipe = $repository->find($id);

        // Vérifier si la recette existe
        if (!$recipe) {
            return new JsonResponse(['error' => 'Recette non trouvée'], Response::HTTP_NOT_FOUND);
        }

        // Sérialiser et retourner la réponse JSON
        $jsonRecipe = $serializer->serialize($recipe, 'json', ["groups" => "getAllRecipes"]);
        return new JsonResponse($jsonRecipe, Response::HTTP_OK, [], true);
    }

    /**
    * Crée une recette avec ses ingrédients, instructions, images et catégories
    */
    #[Route('/api/recipes/create', name: 'create_recipe', methods: ['POST'])]
    public function createRecipe(
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        // Vérifier si l'utilisateur est authentifié
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Récupérer les données JSON de la requête
        $jsonData = $request->get('data');
        if (!$jsonData) {
            return new JsonResponse(['error' => 'Données JSON manquantes'], Response::HTTP_BAD_REQUEST);
        }

        $data = json_decode($jsonData, true);
        if (!$data) {
            return new JsonResponse(['error' => 'JSON invalide'], Response::HTTP_BAD_REQUEST);
        }

        // Vérification des données obligatoires
        $requiredFields = ['name', 'cookingTime', 'description', 'calories', 'quantity', 'preparationTime', 'difficulty', 'ingredients', 'instructions', 'categories'];
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

        // Gestion des catégories
        if (!is_array($data['categories']) || empty($data['categories'])) {
            return new JsonResponse(['error' => 'Les catégories doivent être un tableau non vide'], Response::HTTP_BAD_REQUEST);
        }

        foreach ($data['categories'] as $categoryName) {
            $category = $entityManager->getRepository(Category::class)->findOneBy(['name' => $categoryName]);
            if (!$category) {
                $category = new Category();
                $category->setName($categoryName);
                $entityManager->persist($category);
            }
            $recipe->addCategory($category);
        }

        // Gestion de l'image
        $uploadedFile = $request->files->get('image');
        if ($uploadedFile) {
            if (!$uploadedFile->isValid() || !in_array($uploadedFile->guessExtension(), ['jpg', 'jpeg', 'png'])) {
                return new JsonResponse(['error' => 'Fichier image invalide. Formats acceptés : jpg, jpeg, png.'], Response::HTTP_BAD_REQUEST);
            }

            $newFilename = uniqid() . '.' . $uploadedFile->guessExtension();
            try {
                $uploadedFile->move(
                    $this->getParameter('images_directory'), // Configurez ce paramètre dans services.yaml
                    $newFilename
                );

                $image = new Image();
                $image->setName($newFilename);
                $image->setPath('/uploads/images/' . $newFilename);
                $image->setUploadedAt(new \DateTime());

                $entityManager->persist($image);
                $recipe->setImage($image);
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Erreur lors de l\'upload de l\'image : ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        // Ajout des instructions (conversion en texte si c'est un tableau)
        if (is_array($data['instructions'])) {
            $instructions = implode("\n", $data['instructions']); // Concatène les instructions en string
            $recipe->setInstructions($instructions);
        } else {
            $recipe->setInstructions($data['instructions']);
        }

        // Ajout des ingrédients
        if (!is_array($data['ingredients']) || empty($data['ingredients'])) {
            return new JsonResponse(['error' => 'Les ingrédients doivent être un tableau non vide'], Response::HTTP_BAD_REQUEST);
        }

        foreach ($data['ingredients'] as $ingredient) {
            if (!isset($ingredient['name'], $ingredient['quantity'], $ingredient['unity'])) {
                return new JsonResponse(['error' => 'Données ingrédient incomplètes'], Response::HTTP_BAD_REQUEST);
            }

            // Rechercher ou créer un ingrédient
            $food = $entityManager->getRepository(Food::class)->findOneBy(['name' => $ingredient['name']]);
            if (!$food) {
                $food = new Food();
                $food->setName($ingredient['name']);
                $entityManager->persist($food);
            }

            // Création de la relation entre la recette et l'ingrédient
            $quantityFood = new QuantityFood();
            $quantityFood->setQuantity((int) $ingredient['quantity'])
                ->setUnity($ingredient['unity'])
                ->setRecipeId($recipe)
                ->setFoodId($food);

            $entityManager->persist($quantityFood);
            $recipe->addIngredient($quantityFood);
        }

        $entityManager->persist($recipe);
        $entityManager->flush();

        return new JsonResponse([
            'message' => 'Recette créée avec succès',
            'recipeId' => $recipe->getId()
        ], Response::HTTP_CREATED);
    }
}