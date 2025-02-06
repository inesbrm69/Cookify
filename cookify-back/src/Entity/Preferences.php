<?php

namespace App\Entity;

use App\Repository\PreferencesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PreferencesRepository::class)]
class Preferences
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $diet = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $allergy = null;

    #[ORM\Column]
    private ?int $mealQuantity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDiet(): ?string
    {
        return $this->diet;
    }

    public function setDiet(?string $diet): static
    {
        $this->diet = $diet;

        return $this;
    }

    public function getAllergy(): ?string
    {
        return $this->allergy;
    }

    public function setAllergy(?string $allergy): static
    {
        $this->allergy = $allergy;

        return $this;
    }

    public function getMealQuantity(): ?int
    {
        return $this->mealQuantity;
    }

    public function setMealQuantity(int $mealQuantity): static
    {
        $this->mealQuantity = $mealQuantity;

        return $this;
    }
}
