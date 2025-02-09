<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Category;
use App\Entity\Food;
use App\Entity\Recipes;
use App\Entity\QuantityFood;
use App\Entity\RecipesList;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Ajouter un utilisateur
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->hasher->hashPassword($user, 'password123'));
        $user->setName('John'); // ✅ Ajout du nom obligatoire
        $user->setLastName('Doe'); // ✅ Ajout du prénom obligatoire
        $user->setUsername('johndoe'); // ✅ Ajout du username obligatoire
        $manager->persist($user);

        // Ajouter des catégories (name obligatoire)
        $categoryNames = ['Entrée', 'Plat', 'Dessert', 'Végétarien', 'Vegan'];
        $categories = [];
        foreach ($categoryNames as $name) {
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);
            $categories[] = $category;
        }

        // Ajouter des aliments (name obligatoire)
        $foodNames = ['Pâtes', 'Tomates', 'Crème', 'Œufs', 'Sucre', 'Beurre'];
        $foods = [];
        foreach ($foodNames as $name) {
            $food = new Food();
            $food->setName($name);
            $manager->persist($food);
            $foods[] = $food;
        }

        // Ajouter une recette (name obligatoire)
        $recipe = new Recipes();
        $recipe->setName('Pâtes Carbonara');
        $recipe->setCookingTime(20);
        $recipe->setDescription('Un classique italien.');
        $recipe->setCalories(650);
        $recipe->setQuantity(2);
        $recipe->setPreparationTime(10);
        $recipe->setDifficulty('Facile');
        $recipe->setIsPublic(true);
        $recipe->setCreatedBy($user);
        $recipe->addCategory($categories[1]); // Associe à "Plat"
        $recipe->setInstructions("1. Faire cuire les pâtes\n2. Mélanger la crème et les œufs\n3. Ajouter aux pâtes et servir"); // ✅ Ajout des instructions

        // Ajouter des ingrédients avec quantité (name obligatoire)
        $ingredientData = [
            ['food' => $foods[0], 'quantity' => 200, 'unity' => 'g'], // Pâtes
            ['food' => $foods[2], 'quantity' => 150, 'unity' => 'ml'], // Crème
            ['food' => $foods[3], 'quantity' => 2, 'unity' => 'pcs'], // Œufs
        ];

        foreach ($ingredientData as $data) {
            $quantityFood = new QuantityFood();
            $quantityFood->setQuantity($data['quantity']);
            $quantityFood->setUnity($data['unity']);
            $quantityFood->setFoodId($data['food']);
            $quantityFood->setRecipeId($recipe);
            $manager->persist($quantityFood);
        }

        $manager->persist($recipe);

        // Ajouter une liste de recettes (name obligatoire)
        $recipeList = new RecipesList();
        $recipeList->setName('Ma liste de recettes test');
        $recipeList->setOwner($user);
        $recipeList->addRecipe($recipe);
        
        $manager->persist($recipeList);

        // Sauvegarde en base
        $manager->flush();
    }
}