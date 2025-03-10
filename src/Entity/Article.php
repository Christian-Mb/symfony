<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: "App\Repository\ArticleRepository")]
#[UniqueEntity(fields: ['title'], errorPath: 'title', message: 'This title is already in use')]
#[Vich\Uploadable]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank(message: 'Please enter a title')]
    private string $title;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Please enter content')]
    #[Assert\Length(min: 10, minMessage: 'Content must be at least {{ limit }} characters long')]
    private string $content;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Assert\Url(message: 'Please enter a valid URL')]
    private ?string $image = null;

    #[Vich\UploadableField(mapping: 'property_image', fileNameProperty: 'image')]
    private ?File $imageFile = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Please select an author')]
    private ?User $author = null;

    #[ORM\Column(type: Types::STRING, length: 50)]
    #[Assert\NotBlank(message: 'Please enter a literary genre')]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: 'Literary genre must be at least {{ limit }} characters long',
        maxMessage: 'Literary genre cannot be longer than {{ limit }} characters'
    )]
    private string $genreLitteraire;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'article', orphanRemoval: true)]
    private Collection $comments;

    /**
     * Article constructor.
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * Get the article ID.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the article title.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the article title.
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the article content.
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Set the article content.
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the article image path.
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * Set the article image path.
     */
    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get the article creation date.
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Set the article creation date
     * Note: This should generally not be changed after initial creation.
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt instanceof \DateTimeImmutable
            ? $createdAt
            : new \DateTimeImmutable($createdAt->format('Y-m-d H:i:s'));

        return $this;
    }

    /**
     * Get the article author.
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * Set the article author.
     */
    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get the literary genre of the article.
     */
    public function getGenreLitteraire(): string
    {
        return $this->genreLitteraire;
    }

    /**
     * Set the literary genre of the article.
     */
    public function setGenreLitteraire(string $genreLitteraire): self
    {
        $this->genreLitteraire = $genreLitteraire;

        return $this;
    }

    /**
     * Get the article category.
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * Set the article category.
     */
    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get the comments for this article.
     *
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * Add a comment to this article.
     */
    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setArticle($this);
        }

        return $this;
    }

    /**
     * Remove a comment from this article.
     */
    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // Set the owning side to null if it was part of a bidirectional relationship
            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * Get the image file for this article.
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * Set the image file for this article.
     *
     * If a new file is set, update the updatedAt timestamp
     */
    public function setImageFile(?File $imageFile = null): self
    {
        $this->imageFile = $imageFile;

        // Only update the timestamp if the file is actually a file upload
        if ($imageFile instanceof UploadedFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * Get the last update date.
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Update the article's last update date.
     */
    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        if ($updatedAt === null) {
            $this->updatedAt = null;
        } else {
            $this->updatedAt = $updatedAt instanceof \DateTimeImmutable
                ? $updatedAt
                : new \DateTimeImmutable($updatedAt->format('Y-m-d H:i:s'));
        }

        return $this;
    }

    /**
     * Mark the article as updated now.
     */
    public function markAsUpdated(): self
    {
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    /**
     * Get the formatted creation date.
     */
    public function getFormattedCreatedAt(string $format = 'Y-m-d H:i:s'): string
    {
        return $this->createdAt->format($format);
    }

    /**
     * Get the formatted update date if available.
     */
    public function getFormattedUpdatedAt(string $format = 'Y-m-d H:i:s'): ?string
    {
        return $this->updatedAt?->format($format);
    }
}
