<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;

final class PreferenceController extends AbstractController
{
    #[Route('/preference', name: 'app_preference')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PreferenceController.php',
        ]);
    }

    /**
     * Ajoute ou met à jour les préférences d'un utilisateur
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param PreferenceRepository $preferenceRepository
     * @return JsonResponse
     */
    #[Route('/api/preferences', name: 'preferences.update', methods: ['POST'])]
    public function updatePreferences(
        Request $request,
        EntityManagerInterface $entityManager,
        Security $security,
        PreferenceRepository $preferenceRepository
    ): JsonResponse {
        // Vérifier que l'utilisateur est connecté
        $user = $security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Vous devez être connecté'], 401);
        }

        // Récupérer les préférences de l'utilisateur s'il en a déjà
        $preference = $preferenceRepository->findOneBy(['user' => $user]);

        if (!$preference) {
            // Créer une nouvelle préférence si elle n'existe pas
            $preference = new Preference();
            $preference->setUser($user);
        }

        // Récupérer les données JSON
        $data = json_decode($request->getContent(), true);

        if (isset($data['diet'])) {
            $preference->setDiet($data['diet']);
        }

        if (isset($data['allergy'])) {
            $preference->setAllergy($data['allergy']);
        }

        if (isset($data['mealQuantity'])) {
            $preference->setMealQuantity($data['mealQuantity']);
        }

        // Sauvegarde en base
        $entityManager->persist($preference);
        $entityManager->flush();

        return new JsonResponse([
            'message' => 'Préférences mises à jour',
            'preferences' => [
                'diet' => $preference->getDiet(),
                'allergy' => $preference->getAllergy(),
                'mealQuantity' => $preference->getMealQuantity()
            ]
        ], 200);
    }

    /**
     * Récupère les préférences de l'utilisateur connecté
     *
     * @param Security $security
     * @param PreferenceRepository $preferenceRepository
     * @return JsonResponse
     */
    #[Route('/api/preferences', name: 'preferences.get', methods: ['GET'])]
    public function getPreferences(
        Security $security,
        PreferenceRepository $preferenceRepository
    ): JsonResponse {
        // Vérifier que l'utilisateur est connecté
        $user = $security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Vous devez être connecté'], 401);
        }

        // Récupérer les préférences de l'utilisateur
        $preference = $preferenceRepository->findOneBy(['user' => $user]);

        if (!$preference) {
            return new JsonResponse(['error' => 'Aucune préférence trouvée'], 404);
        }

        return new JsonResponse([
            'preferences' => [
                'diet' => $preference->getDiet(),
                'allergy' => $preference->getAllergy(),
                'mealQuantity' => $preference->getMealQuantity()
            ]
        ], 200);
    }
    
}
