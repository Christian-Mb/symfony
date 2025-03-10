    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    private $title;

    #[ORM\Column(type: "text")]
    #[Assert\Length(min: 10)]
    private $content;

    /**
     * @var string
     */
    #[ORM\Column(type: "string", length: 255)]
    #[Assert\Url]
    private $image;

    /**
     * @var File
     */
    #[Vich\UploadableField(mapping: "property_images", fileNameProperty: "image")]
    private $imageFile;

    #[ORM\Column(type: "datetime")]
    private $createdAt;

    /**
     * Updated at property for handling uploads
     */
    #[ORM\Column(type: "datetime", nullable: true)]
    private $updatedAt;
    #[ORM\Column(type: "string", length: 255)]
    #[Assert\Length(min: 2, max: 255)]
    private $author;

    #[ORM\Column(type: "string", length: 50)]
    #[Assert\Length(min: 3, max: 255)]
    private $genreLitteraire;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Category", inversedBy: "articles")]
    #[ORM\JoinColumn(nullable: false)]
    private $category;

    #[ORM\OneToMany(targetEntity: "App\Entity\Comment", mappedBy: "article", orphanRemoval: true)]
    private $comments;

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
