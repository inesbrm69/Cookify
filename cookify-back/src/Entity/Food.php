<?php

namespace App\Entity;

use App\Repository\FoodRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FoodRepository::class)]
class Food
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    #[Groups("getAllRecipes", "getRecipesByCategorie", "getAllCategories", "getAllFoods")]
    private string $name;


    #[ORM\OneToOne(mappedBy: 'foodId', cascade: ['persist', 'remove'])]
    #[Groups("getAllRecipes", "getRecipesByCategorie", "getAllCategories", "getAllFoods")]
    private ?QuantityFood $quantityFood = null;

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

    public function getQuantityFood(): ?QuantityFood
    {
        return $this->quantityFood;
    }

    public function setQuantityFood(QuantityFood $quantityFood): static
    {
        // set the owning side of the relation if necessary
        if ($quantityFood->getFoodId() !== $this) {
            $quantityFood->setFoodId($this);
        }

        $this->quantityFood = $quantityFood;

        return $this;
    }
}
