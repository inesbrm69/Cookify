<?php

namespace App\Entity;

use App\Repository\RecipesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecipesRepository::class)]
class Recipes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['getAllRecipesLists', 'getAllRecipes', 'getRecipes', 'getRecipesByCategory'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    #[Groups(['getAllRecipesLists', 'getAllRecipes', 'getRecipes', 'getRecipesByCategory'])]
    private string $name;

    #[ORM\Column]
    #[Groups("getAllRecipes")]
    private ?int $cookingTime = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['getAllRecipesLists', 'getAllRecipes', 'getRecipes', 'getRecipesByCategory'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups("getAllRecipes")]
    private ?int $calories = null;

    #[ORM\Column]
    #[Groups("getAllRecipes")]
    private ?int $quantity = null;

    #[ORM\Column]
    #[Groups("getAllRecipes")]
    private ?int $preparationTime = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups("getAllRecipes")]
    private ?string $instructions = null;

    #[ORM\Column(length: 255)]
    #[Groups("getAllRecipes")]
    private ?string $difficulty = null;

    #[ORM\Column]
    #[Groups("getAllRecipes")]
    private ?bool $isPublic = null;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, mappedBy: 'recipes')]
    #[Groups("getAllRecipes")]
    private Collection $categories;

    /**
     * @var Collection<int, Notice>
     */
    #[ORM\OneToMany(targetEntity: Notice::class, mappedBy: 'recipes')]
    #[Groups(["getAllRecipes"])]
    private Collection $notices;

    /**
     * @var Collection<int, QuantityFood>
     */
    #[ORM\OneToMany(targetEntity: QuantityFood::class, mappedBy: 'recipeId', orphanRemoval: true)]
    #[Groups(["getAllRecipes"])]
    private Collection $ingredients;


    #[ORM\ManyToOne(inversedBy: 'recipes')]
    #[Groups("getAllRecipes")]
    private ?User $createdBy = null;

    /**
     * @var Collection<int, RecipesList>
     */
    #[ORM\ManyToMany(targetEntity: RecipesList::class, mappedBy: 'recipes')]
    private Collection $recipesLists;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->notices = new ArrayCollection();
        $this->ingredients = new ArrayCollection();
        $this->recipesLists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCookingTime(): ?int
    {
        return $this->cookingTime;
    }

    public function setCookingTime(int $cookingTime): static
    {
        $this->cookingTime = $cookingTime;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCalories(): ?int
    {
        return $this->calories;
    }

    public function setCalories(int $calories): static
    {
        $this->calories = $calories;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPreparationTime(): ?int
    {
        return $this->preparationTime;
    }

    public function setPreparationTime(int $preparationTime): static
    {
        $this->preparationTime = $preparationTime;

        return $this;
    }

    public function getInstructions(): ?string
    {
        return $this->instructions;
    }

    public function setInstructions(string $instructions): static
    {
        $this->instructions = $instructions;

        return $this;
    }

    public function getDifficulty(): ?string
    {
        return $this->difficulty;
    }

    public function setDifficulty(string $difficulty): static
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    public function isPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): static
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->addRecipe($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        if ($this->categories->removeElement($category)) {
            $category->removeRecipe($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Notice>
     */
    public function getNotices(): Collection
    {
        return $this->notices;
    }

    public function addNotice(Notice $notice): static
    {
        if (!$this->notices->contains($notice)) {
            $this->notices->add($notice);
            $notice->setRecipes($this);
        }

        return $this;
    }

    public function removeNotice(Notice $notice): static
    {
        if ($this->notices->removeElement($notice)) {
            // set the owning side to null (unless already changed)
            if ($notice->getRecipes() === $this) {
                $notice->setRecipes(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, QuantityFood>
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function addIngredient(QuantityFood $ingredient): static
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients->add($ingredient);
            $ingredient->setRecipeId($this);
        }

        return $this;
    }

    public function removeIngredient(QuantityFood $ingredient): static
    {
        if ($this->ingredients->removeElement($ingredient)) {
            // set the owning side to null (unless already changed)
            if ($ingredient->getRecipeId() === $this) {
                $ingredient->setRecipeId(null);
            }
        }

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @return Collection<int, RecipesList>
     */
    public function getRecipesLists(): Collection
    {
        return $this->recipesLists;
    }

    public function addRecipesList(RecipesList $recipesList): static
    {
        if (!$this->recipesLists->contains($recipesList)) {
            $this->recipesLists->add($recipesList);
            $recipesList->addRecipe($this);
        }

        return $this;
    }

    public function removeRecipesList(RecipesList $recipesList): static
    {
        if ($this->recipesLists->removeElement($recipesList)) {
            $recipesList->removeRecipe($this);
        }

        return $this;
    }
}
