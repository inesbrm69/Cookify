<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Category;
use App\Entity\Food;
use App\Entity\Recipes;
use App\Entity\QuantityFood;
use App\Entity\Preferences;
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
        $user->setName('John');
        $user->setLastName('Doe');
        $user->setUsername('johndoe');
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

        $recipeNames = [
            'Pâtes Carbonara',
            'Salade César',
            'Soupe à l’oignon',
            'Tarte au citron',
            'Gratin dauphinois',
            'Lasagnes bolognaises',
            'Pizza Margherita',
            'Ratatouille',
            'Bœuf Bourguignon',
            'Mousse au chocolat'
        ];

        $recipeInstructions = [
            "1. Faire cuire les pâtes\n2. Mélanger la crème et les œufs\n3. Ajouter aux pâtes et servir",
            "1. Préparer la laitue\n2. Ajouter le poulet grillé, les croûtons et la sauce César\n3. Mélanger et servir",
            "1. Faire revenir les oignons\n2. Ajouter le bouillon et laisser mijoter\n3. Servir chaud avec du pain grillé",
            "1. Préparer la pâte\n2. Ajouter la crème au citron\n3. Cuire au four et laisser refroidir",
            "1. Trancher les pommes de terre\n2. Ajouter de la crème et du fromage\n3. Cuire au four jusqu’à ce que ce soit doré",
            "1. Préparer la sauce bolognaise\n2. Monter les couches avec les pâtes\n3. Cuire au four et servir chaud",
            "1. Étaler la pâte à pizza\n2. Ajouter la sauce tomate et la mozzarella\n3. Cuire au four jusqu’à ce que le fromage fonde",
            "1. Couper les légumes\n2. Faire mijoter avec des herbes\n3. Servir chaud ou froid",
            "1. Faire mariner le bœuf\n2. Cuire lentement avec le vin rouge\n3. Servir avec des pommes de terre ou des pâtes",
            "1. Faire fondre le chocolat\n2. Incorporer les œufs et le sucre\n3. Laisser refroidir au réfrigérateur"
        ];

        // Créer les recettes avec leurs données
        $recipes = [];
        foreach ($recipeNames as $index => $name) {
            $recipe = new Recipes();
            $recipe->setName($name);
            $recipe->setCookingTime(rand(15, 120));
            $recipe->setDescription("Description de $name");
            $recipe->setCalories(rand(200, 800)); 
            $recipe->setQuantity(rand(1, 6));
            $recipe->setPreparationTime(rand(5, 30)); 
            $recipe->setDifficulty(['Facile', 'Moyen', 'Difficile'][rand(0, 2)]); 
            $recipe->setIsPublic(true);
            $recipe->setCreatedBy($user);
            
            // Ajout d'une ou plusieurs catégories
            for ($i = 0; $i < rand(1, 2); $i++) {
                $recipe->addCategory($categories[array_rand($categories)]);
            }
            
            $recipe->setInstructions($recipeInstructions[$index]); 
        
            $manager->persist($recipe);
            $recipes[] = $recipe;
        }

        // Ajouter des préférences basées sur les recettes existantes
        foreach ($recipes as $recipe) {
            $preference = new Preferences();
            $preference->setDiet(['Végétarien', 'Vegan', 'Omnivore'][array_rand(['Végétarien', 'Vegan', 'Omnivore'])]);
            $preference->setAllergy(['Aucune', 'Gluten', 'Lactose'][array_rand(['Aucune', 'Gluten', 'Lactose'])]);
            $preference->setMealQuantity($recipe->getQuantity()); // Basé sur la quantité de la recette
            $manager->persist($preference);

            // Associer la préférence à l'utilisateur
            $user->addPreference($preference);
        }

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