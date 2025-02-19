<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\RecipesList;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\RecipesRepository;
use App\Repository\RecipesListRepository;
use App\Repository\PreferencesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

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
    public function getRecipeList(int $listId, RecipesListRepository $recipeListRepository): JsonResponse {
        // Vérifier si l'utilisateur est authentifié
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
        }
        $recipeList = $recipeListRepository->find($listId);
        if (!$recipeList) {
            return new JsonResponse(['error' => 'Liste non trouvée'], 404);
        }
        
        return new JsonResponse([
            'listId' => $recipeList->getId(),
            'name' => $recipeList->getName(),
            'recipes' => array_map(fn($r) => [
                                                'id' => $r->getId(),
                                                'name' => $r->getName(),
                                                'image'  => [
                                                    'name' => $r->getImage()->getName(),
                                                    'path'=>$r->getImage()->getPath()
                                                ],
                                            ],
                                            $recipeList->getRecipes()->toArray()
                                    )
        ], 200);
    }


    /**
     * Génère une liste aléatoire de recettes en fonction des préférences de l'utilisateur
    */
    #[Route('/api/recipelist/generate/{count}', name: 'recipelist.generate', methods: ['POST'])]
    public function generateRecipeList(
        int $count,
        Request $request,
        RecipesRepository $recipesRepository,
        PreferencesRepository $preferencesRepository, // Injection correcte
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ): JsonResponse {
        // Vérifier si l'utilisateur est authentifié
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
        }
        // Vérifier que le nombre demandé est valide
        if ($count <= 0) {
            return new JsonResponse(['error' => 'Le nombre de recettes doit être supérieur à 0'], Response::HTTP_BAD_REQUEST);
        }

        // Décoder les préférences envoyées dans la requête
        $data = json_decode($request->getContent(), true);
        if ($data === null || !isset($data['preferences'])) {
            return new JsonResponse(['error' => 'Les préférences sont requises'], Response::HTTP_BAD_REQUEST);
        }

        $preferences = $data['preferences'];
        $diet = $preferences['diet'] ?? null; // Préférence de régime alimentaire
        $allergy = $preferences['allergy'] ?? null; // Préférence d'allergie

        // Vérifier qu'au moins un des champs (diet ou allergy) est fourni
        if (!$diet && !$allergy) {
            return new JsonResponse(
                ['error' => 'Veuillez spécifier au moins un régime alimentaire (diet) ou une allergie (allergy)'],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Recherche des recettes en fonction des préférences
        $recipes = [];
        if ($diet) {
            $dietPreference = $preferencesRepository->findOneBy(['diet' => $diet]);
            if (!$dietPreference) {
                return new JsonResponse(['error' => 'Le régime alimentaire spécifié n\'existe pas'], Response::HTTP_BAD_REQUEST);
            }
            $recipes = $recipesRepository->findByDiet($diet);
        }

        if ($allergy) {
            $allergyPreference = $preferencesRepository->findOneBy(['allergy' => $allergy]);
            if (!$allergyPreference) {
                return new JsonResponse(['error' => 'L\'allergie spécifiée n\'existe pas'], Response::HTTP_BAD_REQUEST);
            }

            $recipesByAllergy = new ArrayCollection($recipesRepository->findByAllergy($allergy));

            // Fusionner les recettes si `diet` est aussi fourni
            if ($diet) {
                $recipes = (new ArrayCollection($recipes))
                    ->filter(fn($recipe) => $recipesByAllergy->contains($recipe))
                    ->toArray();
            } else {
                $recipes = $recipesByAllergy->toArray();
            }
        }

        // Vérifier qu'il y a des recettes disponibles
        if (empty($recipes)) {
            return new JsonResponse(['error' => 'Aucune recette ne correspond à vos préférences'], Response::HTTP_NOT_FOUND);
        }

        // Vérifier que le nombre demandé est possible
        if (count($recipes) < $count) {
            return new JsonResponse(
                ['error' => 'Pas assez de recettes correspondant à vos préférences (' . count($recipes) . ' trouvées pour ' . $count . ' demandées)'],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Mélanger et sélectionner les recettes demandées
        shuffle($recipes);
        $selectedRecipes = array_slice($recipes, 0, $count);

        // Créer une liste de recettes
        $recipesList = new RecipesList();
        $recipesList->setName("Liste générée le " . (new \DateTime())->format('d/m/Y H:i:s'));

        foreach ($selectedRecipes as $recipe) {
            $recipesList->addRecipe($recipe);
        }

        $entityManager->persist($recipesList);
        $entityManager->flush();

        // Retourner la liste des recettes
        return new JsonResponse(
            $serializer->serialize($recipesList, 'json', [
                'groups' => ['getAllRecipesLists']
            ]),
            Response::HTTP_CREATED,
            [],
            true
        );
    }

    /**
     * Supprime une recette d'une liste sans la supprimer en base
     *
     * @param int $listId
     * @param int $recipeId
     * @param RecipesListRepository $recipeListRepository
     * @param RecipesRepository $recipesRepository
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[Route('/api/recipelist/{listId}/remove/{recipeId}', name: 'recipelist.removeRecipe', methods: ['DELETE'])]
    public function removeRecipeFromList(
        int $listId,
        int $recipeId,
        RecipesListRepository $recipeListRepository,
        RecipesRepository $recipesRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        // Vérifier si l'utilisateur est authentifié
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
        }
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

    /**
     * Met à jour une recette dans une liste en remplaçant une recette par une autre
     *
     * @param int $listId
     * @param int $oldRecipeId
     * @param int $newRecipeId
     * @param RecipesListRepository $recipeListRepository
     * @param RecipesRepository $recipesRepository
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[Route('/api/recipelist/{listId}/update/{oldRecipeId}/with/{newRecipeId}', name: 'recipelist.updateRecipe', methods: ['PUT'])]
    public function updateRecipeInList(
        int $listId,
        int $oldRecipeId,
        int $newRecipeId,
        RecipesListRepository $recipeListRepository,
        RecipesRepository $recipesRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        // Vérifier si l'utilisateur est authentifié
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
        }
        // Récupérer la liste de recettes
        $recipeList = $recipeListRepository->find($listId);
        if (!$recipeList) {
            return new JsonResponse(['error' => 'Liste non trouvée'], 404);
        }

        // Récupérer l'ancienne recette
        $oldRecipe = $recipesRepository->find($oldRecipeId);
        if (!$oldRecipe) {
            return new JsonResponse(['error' => 'Ancienne recette non trouvée'], 404);
        }

        // Vérifier si l'ancienne recette est dans la liste
        if (!$recipeList->getRecipes()->contains($oldRecipe)) {
            return new JsonResponse(['error' => 'La recette à remplacer n\'est pas dans cette liste'], 400);
        }

        // Récupérer la nouvelle recette
        $newRecipe = $recipesRepository->find($newRecipeId);
        if (!$newRecipe) {
            return new JsonResponse(['error' => 'Nouvelle recette non trouvée'], 404);
        }

        // Remplacer l'ancienne recette par la nouvelle
        $recipeList->removeRecipe($oldRecipe);
        $recipeList->addRecipe($newRecipe);

        // Sauvegarder les changements
        $entityManager->flush();

        return new JsonResponse([
            'message' => 'Recette mise à jour avec succès dans la liste',
            'updatedList' => array_map(fn($r) => ['id' => $r->getId(), 'name' => $r->getName()], $recipeList->getRecipes()->toArray())
        ], 200);
    }
}
