<?php

namespace App\Entity;

use App\Repository\FoodRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FoodRepository::class)]
class Food
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToOne(mappedBy: 'foodId', cascade: ['persist', 'remove'])]
    private ?QuantityFood $quantityFood = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
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
