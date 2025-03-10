<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[UniqueEntity(fields: ['title'], message: 'This category title is already used')]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank(message: 'The title cannot be empty')]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'The title must be at least {{ limit }} characters long',
        maxMessage: 'The title cannot be longer than {{ limit }} characters'
    )]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(
        max: 5000,
        maxMessage: 'The description cannot be longer than {{ limit }} characters'
    )]
    private ?string $description = null;

    #[ORM\OneToMany(targetEntity: Article::class, mappedBy: 'category')]
    private Collection $articles;

    /**
     * Category constructor.
     */
    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    /**
     * String representation of the category.
     */
    public function __toString(): string
    {
        return $this->title ?? 'New Category';
    }

    /**
     * Get the category ID.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the category title.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set the category title.
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the category description.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the category description.
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get articles in this category.
     *
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    /**
     * Add an article to this category.
     */
    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setCategory($this);
        }

        return $this;
    }

    /**
     * Remove an article from this category.
     */
    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getCategory() === $this) {
                $article->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * Count articles in this category.
     */
    public function getArticleCount(): int
    {
        return $this->articles->count();
    }
}
