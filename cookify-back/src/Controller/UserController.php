<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }

    #[Route('/api/register', name: 'user_register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user->setUsername($data['username']);
        $user->setPassword($data['password']); // Mot de passe enregistré en clair (pas recommandé en production)

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'User registered successfully'], Response::HTTP_CREATED);
    }

    #[Route('/api/login', name: 'user_login', methods: ['POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($error) {
            return new JsonResponse(['error' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse(['message' => 'Login successful', 'user' => $lastUsername]);
    }
}
