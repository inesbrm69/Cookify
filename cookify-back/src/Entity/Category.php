<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    public const TYPE_DIET = 'diet';
    public const TYPE_ALLERGY = 'allergy';
    public const TYPE_COURSE = 'course';
    
    public const VALID_TYPES = [
        self::TYPE_DIET,
        self::TYPE_ALLERGY,
        self::TYPE_COURSE
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("getAllRecipes")]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    #[Groups(["getAllRecipes", "getRecipesByCategorie", "getAllCategories"])]
    private string $name;

    #[ORM\Column(length: 255, nullable: false)]
    #[Groups(["getAllRecipes", "getAllCategories"])]
    private string $type = self::TYPE_COURSE;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["getAllCategories"])]
    private ?string $description = null;

    /**
     * @var Collection<int, Recipes>
     */
    #[ORM\ManyToMany(targetEntity: Recipes::class, inversedBy: 'categories')]
    private Collection $recipes;

    public function __construct()
    {
        $this->recipes = new ArrayCollection();
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

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        if (!in_array($type, self::VALID_TYPES)) {
            throw new \InvalidArgumentException('Type invalide');
        }
        
        $this->type = $type;
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

    /**
     * @return Collection<int, Recipes>
     */
    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    public function addRecipe(Recipes $recipe): static
    {
        if (!$this->recipes->contains($recipe)) {
            $this->recipes->add($recipe);
        }
        return $this;
    }

    public function removeRecipe(Recipes $recipe): static
    {
        $this->recipes->removeElement($recipe);
        return $this;
    }
    
    public function isDiet(): bool
    {
        return $this->type === self::TYPE_DIET;
    }
    
    public function isAllergy(): bool
    {
        return $this->type === self::TYPE_ALLERGY;
    }
    
    public function isCourse(): bool
    {
        return $this->type === self::TYPE_COURSE;
    }
}