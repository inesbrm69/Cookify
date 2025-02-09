<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\FoodRepository;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;

final class FoodController extends AbstractController
{
    #[Route('/food', name: 'app_food')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/FoodController.php',
        ]);
    }

     /**
     * Renvoie tous les aliments
     *
     * @param FoodRepository $repository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/foods', name: 'food.getAll', methods:['GET'])]
    public function getAllFoods(
        FoodRepository $repository,
        SerializerInterface $serializer
        ): JsonResponse
    {
        $food =  $repository->findAll();
        $jsonFood = $serializer->serialize($categ, 'json',["groups" => "getAllFoods"]);
        return new JsonResponse(    
            $jsonCateg,
            Response::HTTP_OK, 
            [], 
            true
        );
    }
}
