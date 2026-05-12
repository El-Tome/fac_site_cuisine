<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IngredientRepository::class)]
class Ingredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * @var Collection<int, IngredientCategories>
     */
    #[ORM\ManyToMany(targetEntity: IngredientCategories::class, inversedBy: 'ingredients')]
    private Collection $categories;

    /**
     * @var Collection<int, RecipeIngredients>
     */
    #[ORM\OneToMany(targetEntity: RecipeIngredients::class, mappedBy: 'ingredient', orphanRemoval: true)]
    private Collection $ingredientUsage;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->ingredientUsage = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, IngredientCategories>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(IngredientCategories $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(IngredientCategories $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    /**
     * @return Collection<int, RecipeIngredients>
     */
    public function getIngredientUsage(): Collection
    {
        return $this->ingredientUsage;
    }

    public function addIngredientUsage(RecipeIngredients $ingredientUsage): static
    {
        if (!$this->ingredientUsage->contains($ingredientUsage)) {
            $this->ingredientUsage->add($ingredientUsage);
            $ingredientUsage->setIngredient($this);
        }

        return $this;
    }

    public function removeIngredientUsage(RecipeIngredients $ingredientUsage): static
    {
        if ($this->ingredientUsage->removeElement($ingredientUsage)) {
            // set the owning side to null (unless already changed)
            if ($ingredientUsage->getIngredient() === $this) {
                $ingredientUsage->setIngredient(null);
            }
        }

        return $this;
    }
}
