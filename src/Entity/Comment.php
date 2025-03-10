<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Comment entity for storing user comments on articles.
 */
#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Comment implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'The comment cannot be empty')]
    #[Assert\Length(
        min: 5,
        minMessage: 'Your comment must be at least {{ limit }} characters long'
    )]
    private string $content;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(targetEntity: Article::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Article $article = null;

    /**
     * Constructor with automatic timestamp initialization.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * String representation of the comment.
     */
    public function __toString(): string
    {
        return (string) $this->getContent();
    }

    /**
     * Get the comment ID.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the author (User entity).
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * Set the author (User entity).
     */
    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get the author's username.
     */
    public function getAuthorName(): string
    {
        return $this->author ? $this->author->getUsername() : '';
    }

    /**
     * Get the comment content.
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Set the comment content.
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the creation timestamp.
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Set the creation timestamp.
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the associated article.
     */
    public function getArticle(): ?Article
    {
        return $this->article;
    }

    /**
     * Set the associated article.
     */
    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Automatically set creation timestamp before persist.
     */
    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}
