<?php

namespace App\Entity;

use App\Repository\QuantityFoodRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuantityFoodRepository::class)]
class QuantityFood
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column(length: 255)]
    private ?string $unity = null;

    #[ORM\ManyToOne(inversedBy: 'ingredients')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Recipes $recipeId = null;

    #[ORM\OneToOne(inversedBy: 'quantityFood', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Food $foodId = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUnity(): ?string
    {
        return $this->unity;
    }

    public function setUnity(string $unity): static
    {
        $this->unity = $unity;

        return $this;
    }

    public function getRecipeId(): ?Recipes
    {
        return $this->recipeId;
    }

    public function setRecipeId(?Recipes $recipeId): static
    {
        $this->recipeId = $recipeId;

        return $this;
    }

    public function getFoodId(): ?Food
    {
        return $this->foodId;
    }

    public function setFoodId(Food $foodId): static
    {
        $this->foodId = $foodId;

        return $this;
    }
}
