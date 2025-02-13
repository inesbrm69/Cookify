<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;

final class QuantityFoodController extends AbstractController
{
    #[Route('/quantity/food', name: 'app_quantity_food')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/QuantityFoodController.php',
        ]);
    }

    #[Route('/api/quantityfood/recipe/{recipeId}', name: 'quantityfood.getByRecipe', methods: ['GET'])]
    public function getQuantitiesByRecipe(int $recipeId, RecipesRepository $recipesRepository): JsonResponse {
        $recipe = $recipesRepository->find($recipeId);
        if (!$recipe) {
            return new JsonResponse(['error' => 'Recette non trouvée'], 404);
        }

        $quantities = $recipe->getIngredients(); // Relation OneToMany avec QuantityFood

        return new JsonResponse([
            'recipe' => ['id' => $recipe->getId(), 'name' => $recipe->getName()],
            'ingredients' => array_map(fn($q) => [
                'id' => $q->getId(),
                'food' => ['id' => $q->getFoodId()->getId(), 'name' => $q->getFoodId()->getName()],
                'quantity' => $q->getQuantity(),
                'unity' => $q->getUnity()
            ], $quantities->toArray())
        ], 200);
    }

    #[Route('/api/quantityfood/delete/{quantityId}', name: 'quantityfood.delete', methods: ['DELETE'])]
    public function deleteQuantity(int $quantityId, EntityManagerInterface $entityManager, QuantityFoodRepository $quantityFoodRepository): JsonResponse {
        $quantityFood = $quantityFoodRepository->find($quantityId);
        if (!$quantityFood) {
            return new JsonResponse(['error' => 'Quantité non trouvée'], 404);
        }

        $entityManager->remove($quantityFood);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Quantité supprimée avec succès'], 200);
    }

}
